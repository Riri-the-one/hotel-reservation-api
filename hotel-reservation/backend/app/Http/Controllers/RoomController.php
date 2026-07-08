<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index()
    {
        // On renvoie les chambres avec les informations de leur type (relation)
        return response()->json(Room::with('roomType')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|unique:rooms',
            'status' => 'required|in:disponible,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validation de l'image
        ]);

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room = Room::create($validated);

        return response()->json($room, 201);
    }

    public function show(Room $room)
    {
        return response()->json($room->load('roomType'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_type_id' => 'sometimes|required|exists:room_types,id',
            'room_number' => 'sometimes|required|string|unique:rooms,room_number,' . $room->id,
            'status' => 'sometimes|required|in:disponible,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Supprime l'ancienne image si elle existe
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($validated);

        return response()->json($room);
    }

    public function destroy(Room $room)
    {
        // Supprime l'image associée avant de supprimer la chambre
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }
        
        $room->delete();

        return response()->json(['message' => 'Chambre supprimée avec succès']);
    }
}