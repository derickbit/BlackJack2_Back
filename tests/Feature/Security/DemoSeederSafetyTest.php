<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\AtualizacaoSeeder;
use Database\Seeders\DatabaseSeeder;
use Tests\SecurityTestCase;

class DemoSeederSafetyTest extends SecurityTestCase
{
    public function test_demo_seeders_refuse_production_without_creating_accounts(): void
    {
        $this->app['env'] = 'production';

        foreach ([DatabaseSeeder::class, AtualizacaoSeeder::class] as $seeder) {
            try {
                $this->app->make($seeder)->run();
                $this->fail('The demo seeder should refuse production.');
            } catch (\RuntimeException $exception) {
                $this->assertStringContainsString('produção', $exception->getMessage());
            }
        }

        $this->assertSame(0, User::count());
    }
}
