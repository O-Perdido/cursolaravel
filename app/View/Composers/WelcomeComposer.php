<?php
namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Termo;

class WelcomeComposer
{
    public function compose(View $view): void
    {
        $termos = Termo::with(['estagiario', 'rescisao'])
            ->whereDoesntHave('rescisao')
            ->get();
        $view->with('termos', $termos);
    }
}
