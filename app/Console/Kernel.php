<?php

namespace App\Console;

use App\Jobs\GerarAvaliacoesAutomaticasJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Gerar avaliações de 6 meses automaticamente
        // Executa diariamente às 02:00 da manhã via cron do HostGator
        $schedule->command('avaliacoes:gerar-automaticas')->dailyAt('02:00');

        // Você pode adicionar outros agendamentos aqui
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
