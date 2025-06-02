<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use Carbon\Carbon;

class DeleteOldClosedReports extends Command
{
    protected $signature = 'reports:delete-old-closed';
    protected $description = 'Exclui reports concluídos há mais de 30 dias';

    public function handle()
    {
        $deleted = Report::where('status', 'concluído')
            ->where('updated_at', '<', now()->subDays(30))
            ->delete();

        $this->info("Reports excluídos: $deleted");
    }
}
