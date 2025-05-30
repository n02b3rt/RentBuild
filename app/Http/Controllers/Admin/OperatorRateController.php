<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment;

class OperatorRateController extends Controller
{
    /** Pokaż listę unikalnych kategorii ze stawkami */
    public function index()
    {
        // pobieramy każdą kategorię i jej (identyczną) stawkę
        $rates = Equipment::query()
            ->select('category', DB::raw('MAX(operator_rate) as operator_rate'))
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return view('admin.operator_rates.index', compact('rates'));
    }

    /** Formularz edycji dla jednej kategorii */
    public function edit($category)
    {
        // zabezpiecz nazwę kategorii (URL encoded)
        $decoded = urldecode($category);

        // wybieramy aktualną stawkę (może być null albo 0)
        $current = Equipment::where('category', $decoded)
            ->value('operator_rate');

        return view('admin.operator_rates.edit', [
            'category'         => $decoded,
            'current_operator_rate' => $current,
        ]);
    }

    /** Zapisujemy nową stawkę – masowo dla wszystkich sprzętów z tą kategorią */
    public function update(Request $request, $category)
    {
        $decoded = urldecode($category);

        $data = $request->validate([
            'operator_rate' => ['required','numeric','min:0'],
        ]);

        Equipment::where('category', $decoded)
            ->update(['operator_rate' => $data['operator_rate']]);

        return redirect()->route('admin.operator-rates.index')
            ->with('success', "Stawkę dla kategorii “{$decoded}” ustawiono na {$data['operator_rate']} zł/dzień.");
    }
}
