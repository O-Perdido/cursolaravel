<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\View\Composers\MainComposer;
use App\View\Composers\WelcomeComposer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;




class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.main', MainComposer::class);
        View::composer('welcome', WelcomeComposer::class);

        // Compositor de view para o sidebar
        View::composer('components.sidebar', function ($view) {

            // Obter os dados necessários
            $supervisores = \App\Models\Supervisor::all();
            $user = Auth::user();
            $userName = $user ? $user->name : 'Visitante';

            // Preparar os dados para o sidebar
            $sidebarData = [
                'supervisores' => $supervisores,
                'userName' => $userName,
            ];

            // Passar os dados para a view
            $view->with($sidebarData);
        });

        // Usar componentes de paginação do Bootstrap 5
        if (method_exists(Paginator::class, 'useBootstrapFive')) {
            Paginator::useBootstrapFive();
        }
    }
}
