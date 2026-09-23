<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin() || (string) $user->id === (string) $report->user_id;
    }

    public function reply(User $user, Report $report): bool
    {
        return $this->view($user, $report);
    }

    public function updateStatus(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Report $report): bool
    {
        return $this->view($user, $report);
    }
}
