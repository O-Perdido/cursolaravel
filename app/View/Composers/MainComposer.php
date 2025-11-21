<?php
namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Termo;

class MainComposer
{
    public function compose(View $view): void
    {
        $termos = Termo::all();
        $view->with('termos', $termos);
    }
}
