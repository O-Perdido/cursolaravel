<?php
namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Termo;
use App\Models\Vaga;
use Illuminate\Support\Facades\Auth;

class MainComposer
{
    public function compose(View $view): void
    {
        $termos = Termo::all();
        $vagaQuery = Vaga::whereNull('fk_id_termo')
            ->where('status', 'disponivel');

        if (Auth::check() && Auth::user()->nivel === 'empresa') {
            $vagaQuery->where('fk_id_empresa', Auth::user()->fk_id_empresa);
        }

        $vagasAbertasCount = $vagaQuery->count();

        $view->with([
            'termos' => $termos,
            'vagasAbertasCount' => $vagasAbertasCount,
        ]);
    }
}
