<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminEquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query();
        if ($search = $request->input('search')) {
            $query->where('name', 'ilike', "%{$search}%");
        }
        $equipments = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('admin.equipment.index', compact('equipments'));
    }

    public function create()
    {
        return view('admin.equipment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'required|string',
            'availability'    => 'required|in:dostepny,niedostepny,rezerwacja',
            'rental_price'    => 'required|numeric|min:0',
            'thumbnail'       => 'required|mimes:webp|max:4096',
            'photos.*'        => 'nullable|mimes:webp|max:4096',
            'technical_state' => 'required|in:nowy,uzywany,naprawa',
            'category'        => 'required|string|max:255',
            'operator_rate'   => 'required|numeric|min:0',
        ]);

        // Jeśli kategoria już istnieje, nadpisz stawką z bazy
        if (Equipment::where('category', $validated['category'])->exists()) {
            $validated['operator_rate'] = Equipment::where('category', $validated['category'])
                ->value('operator_rate');
        }

        // Domyślnie
        $validated['number_of_rentals'] = 0;

        $equipment = new Equipment($validated);

        // Folder bazowany na kategorii i UUID
        $slug          = Str::slug($equipment->category);
        $folderName    = "{$slug}-" . Str::uuid();
        $storageFolder = "sprzety/{$folderName}";

        // Miniatura
        $thumbPath = $request->file('thumbnail')
            ->storeAs($storageFolder, 'glowne.webp', 'public');
        $equipment->thumbnail     = "storage/{$thumbPath}";

        // Dodatkowe zdjęcia
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $i => $photo) {
                $photo->storeAs($storageFolder, "photo_".time()."_{$i}.webp", 'public');
            }
        }
        $equipment->folder_photos = "storage/{$storageFolder}/";

        $equipment->save();

        return redirect()
            ->route('admin.equipment.index')
            ->with('success', 'Sprzęt został dodany.');
    }

    public function edit($id)
    {
        $equipment  = Equipment::findOrFail($id);
        $categories = Equipment::select('category')->distinct()->pluck('category');
        return view('admin.equipment.edit', compact('equipment','categories'));
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'required|string',
            'availability'    => 'required|in:dostepny,niedostepny,rezerwacja',
            'rental_price'    => 'required|numeric|min:0',
            'thumbnail'       => 'nullable|mimes:webp|max:4096',
            'photos.*'        => 'nullable|mimes:webp|max:4096',
            'technical_state' => 'required|in:nowy,uzywany,naprawa',
            'category'        => 'required|string|max:255',
            'operator_rate'   => 'required|numeric|min:0',
        ]);

        // Jeśli kategoria istnieje (inna pozycja), pobierz jej stawkę
        $exists = Equipment::where('category', $validated['category'])
            ->where('id','!=',$equipment->id)
            ->exists();
        if ($exists) {
            $validated['operator_rate'] = Equipment::where('category', $validated['category'])
                ->value('operator_rate');
        }

        // Folder bazowany na kategorii i ID
        $slug          = Str::slug($validated['category']);
        $storageFolder = "sprzety/{$slug}-{$equipment->id}";

        // Nowa miniatura?
        if ($request->hasFile('thumbnail')) {
            // Usuń starą
            if ($equipment->thumbnail) {
                Storage::disk('public')->delete(
                    Str::after($equipment->thumbnail, 'storage/')
                );
            }
            $path = $request->file('thumbnail')
                ->storeAs($storageFolder, 'glowne.webp', 'public');
            $equipment->thumbnail = "storage/{$path}";
        }

        // Nowe zdjęcia
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $i => $photo) {
                $photo->storeAs($storageFolder, "photo_".time()."_{$i}.webp", 'public');
            }
        }

        // Aktualizacja pozostałych pól
        $equipment->fill([
            'name'            => $validated['name'],
            'description'     => $validated['description'],
            'availability'    => $validated['availability'],
            'rental_price'    => $validated['rental_price'],
            'technical_state' => $validated['technical_state'],
            'category'        => $validated['category'],
            'operator_rate'   => $validated['operator_rate'],
            'folder_photos'   => "storage/{$storageFolder}/",
        ]);

        $equipment->save();

        return redirect()
            ->route('admin.equipment.index')
            ->with('success', 'Sprzęt został zaktualizowany.');
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        return redirect()
            ->route('admin.equipment.index')
            ->with('success', 'Sprzęt został usunięty.');
    }
}
