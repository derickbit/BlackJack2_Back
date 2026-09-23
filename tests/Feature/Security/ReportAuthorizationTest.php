<?php

namespace Tests\Feature\Security;

use App\Models\Report;
use App\Models\ReportMessage;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\SecurityTestCase;

class ReportAuthorizationTest extends SecurityTestCase
{
    private function report(User $owner): Report
    {
        $report = Report::create(['user_id' => $owner->id, 'titulo' => 'Test support request', 'status' => 'aberto']);
        $report->messages()->create(['user_id' => $owner->id, 'mensagem' => 'Private test message']);

        return $report;
    }

    public function test_guest_cannot_access_support(): void
    {
        $report = $this->report(User::factory()->create());
        $this->getJson('/api/reports/'.$report->id)->assertUnauthorized();
        $this->getJson('/api/reports/'.$report->id.'/messages')->assertUnauthorized();
        $this->postJson('/api/reports/'.$report->id.'/messages', ['mensagem' => 'Test'])->assertUnauthorized();
    }

    public function test_other_user_cannot_read_reply_change_status_or_delete_report(): void
    {
        $report = $this->report(User::factory()->create());
        Sanctum::actingAs(User::factory()->create());
        $url = '/api/reports/'.$report->id;

        $this->getJson($url)->assertForbidden();
        $this->getJson($url.'/messages')->assertForbidden();
        $this->postJson($url.'/messages', ['mensagem' => 'Unauthorized test'])->assertForbidden();
        $this->putJson($url, ['status' => 'concluído'])->assertForbidden();
        $this->patchJson($url, ['status' => 'concluído'])->assertForbidden();
        $this->patchJson($url.'/status', ['status' => 'concluído'])->assertForbidden();
        $this->deleteJson($url)->assertForbidden();
        $this->assertModelExists($report);
        $this->assertSame('aberto', $report->fresh()->status);
        $this->assertSame(1, $report->messages()->count());
    }

    public function test_owner_can_read_and_reply_but_cannot_change_status(): void
    {
        $owner = User::factory()->create();
        $report = $this->report($owner);
        Sanctum::actingAs($owner);
        $url = '/api/reports/'.$report->id;

        $this->getJson($url)->assertOk()->assertJsonPath('id', $report->id);
        $messages = $this->getJson($url.'/messages')->assertOk()->json();
        $this->assertArrayNotHasKey('email', $messages[0]['user']);
        $this->postJson($url.'/messages', ['mensagem' => 'Owner reply'])->assertCreated();
        $this->putJson($url, ['status' => 'concluído'])->assertForbidden();
        $this->patchJson($url.'/status', ['status' => 'concluído'])->assertForbidden();
        $this->assertSame(2, $report->messages()->count());
    }

    public function test_owner_can_delete_own_report(): void
    {
        $owner = User::factory()->create();
        $report = $this->report($owner);
        Sanctum::actingAs($owner);
        $this->deleteJson('/api/reports/'.$report->id)->assertOk();
        $this->assertModelMissing($report);
    }

    public function test_admin_can_read_reply_change_status_and_delete_report(): void
    {
        $report = $this->report(User::factory()->create());
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $url = '/api/reports/'.$report->id;

        $this->getJson($url)->assertOk();
        $this->getJson($url.'/messages')->assertOk();
        $this->postJson($url.'/messages', ['mensagem' => 'Admin reply'])->assertCreated();
        $this->putJson($url, ['status' => 'em_análise'])->assertOk();
        $this->patchJson($url.'/status', ['status' => 'concluído'])->assertOk();
        $this->assertSame('concluído', $report->fresh()->status);
        $this->deleteJson($url)->assertOk();
        $this->assertModelMissing($report);
    }

    public function test_closed_report_rejects_new_messages_even_from_owner(): void
    {
        $owner = User::factory()->create();
        $report = $this->report($owner);
        $report->update(['status' => 'concluído']);
        Sanctum::actingAs($owner);
        $this->postJson('/api/reports/'.$report->id.'/messages', ['mensagem' => 'Late reply'])->assertForbidden();
        $this->assertSame(1, $report->messages()->count());
    }

    public function test_index_only_contains_own_reports(): void
    {
        $owner = User::factory()->create();
        $ownReport = $this->report($owner);
        $this->report(User::factory()->create());
        Sanctum::actingAs($owner);
        $this->getJson('/api/reports')->assertOk()->assertJsonCount(1)->assertJsonPath('0.id', $ownReport->id);
    }

    public function test_support_creation_uses_authenticated_user_not_submitted_user_id(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($owner);
        $this->postJson('/api/reports', [
            'user_id' => $other->id,
            'titulo' => 'New support request',
            'mensagem' => 'Test message',
        ])->assertCreated()->assertJsonPath('report.user_id', $owner->id);
        $this->assertSame($owner->id, ReportMessage::firstOrFail()->user_id);
    }
}
