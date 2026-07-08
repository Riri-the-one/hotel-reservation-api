<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    // Lister tous les types de chambres
    public function index()
    {
        return response()->json(RoomType::all());
    }

    // Créer un nouveau type de chambre
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
        ]);

        $roomType = RoomType::create($validated);

        return response()->json($roomType, 201);
    }

    // Afficher un type de chambre spécifique
    public function show(RoomType $roomType)
    {
        return response()->json($roomType);
    }

    // Mettre à jour un type de chambre
    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'sometimes|required|integer|min:1',
        ]);

        $roomType->update($validated);

        return response()->json($roomType);
    }

    // Supprimer un type de chambre
    public function destroy(RoomType $roomType)
    {
        $roomType->delete();

        return response()->json(['message' => 'Type de chambre supprimé avec succès']);
    }
}