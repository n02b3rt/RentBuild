<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Sprzet;

class MostRented extends Component
{
    public $sprzety;

    public function __construct()
    {
        $this->sprzety = Sprzet::orderByDesc('ilosc_wypozyczen')->limit(4)->get();
    }

    public function render()
    {
        return view('components.most-rented');
    }
}
