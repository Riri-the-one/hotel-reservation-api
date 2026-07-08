<?php

namespace App\Http\Controllers;

use App\Models\Tariff;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    // 1. Voir tous les tarifs (Read)
    public function index()
    {
        return response()->json(Tariff::all(), 200);
    }

    // 2. Créer un nouveau tarif (Create)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $tariff = Tariff::create($validated);
        return response()->json($tariff, 201);
    }

    // 3. Voir un tarif spécifique
    public function show(Tariff $tariff)
    {
        return response()->json($tariff, 200);
    }

    // 4. Modifier un tarif (Update)
    public function update(Request $request, Tariff $tariff)
    {
        $validated = $request->validate([
            'price' => 'numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $tariff->update($validated);
        return response()->json($tariff, 200);
    }

    // 5. Supprimer un tarif (Delete)
    public function destroy(Tariff $tariff)
    {
        $tariff->delete();
        return response()->json(['message' => 'Tarif supprimé avec succès'], 200);
    }
}