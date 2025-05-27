<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminEquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();
        return view('admin.equipment.index', compact('equipments'));
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        return redirect()->route('admin.equipment.index')->with('success', 'Sprzęt został usunięty.');
    }

    public function create()
    {
        return view('admin.equipment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'availability' => 'required|in:dostepny,niedostepny,rezerwacja',
            'rental_price' => 'required|numeric|min:0',
            'thumbnail' => 'required|mimes:webp|max:4096',
            'photos.*' => 'nullable|mimes:webp|max:4096',
            'technical_state' => 'required|in:nowy,uzywany,naprawa',
            'category' => 'required|string|max:255',
        ]);

        // 📦 Wartości domyślne
        $validated['promotion_type'] = null;
        $validated['discount'] = null;
        $validated['start_datetime'] = null;
        $validated['end_datetime'] = null;
        $validated['number_of_rentals'] = 0;

        // 🛠️ Tworzymy sprzęt bez zdjęć
        $equipment = Equipment::create($validated);

        // 🔠 Tworzymy folder: nazwa-kategorii-bez-spacji + ID
        $categorySlug = str_replace(' ', '-', $equipment->category);
        $folderName = $categorySlug . '-' . $equipment->id;
        $storageFolder = 'sprzety/' . $folderName;

        // 🎯 Miniatura jako glowne.webp
        if ($request->hasFile('thumbnail')) {
            $thumbnailFile = $request->file('thumbnail');
            $thumbnailPath = $thumbnailFile->storeAs($storageFolder, 'glowne.webp', 'public');
            $equipment->thumbnail = 'storage/' . $thumbnailPath;
        }

        // 📸 Dodatkowe zdjęcia
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $photo->storeAs($storageFolder, 'photo_' . time() . '_' . $index . '.webp', 'public');
            }
            $equipment->folder_photos = 'storage/' . $storageFolder . '/';
        }

        $equipment->save();

        return redirect()->route('admin.equipment.index')->with('success', 'Sprzęt został dodany.');
    }

    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        $categories = Equipment::select('category')->distinct()->pluck('category');

        return view('admin.equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'availability' => 'required|in:dostepny,niedostepny,rezerwacja',
            'rental_price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|mimes:webp|max:4096',
            'photos.*' => 'nullable|mimes:webp|max:4096',
            'technical_state' => 'required|in:nowy,uzywany,naprawa',
            'category' => 'required|string|max:255',
            'promotion_type' => 'nullable|in:kategoria,pojedyncza',
            'discount' => 'nullable|numeric|min:0|max:100',
            'start_datetime' => 'nullable|date',
            'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
        ]);

        // 🔁 folder jak wcześniej: slug-kategorii + id
        $categorySlug = Str::slug($validated['category']);
        $folder = $categorySlug . '-' . $equipment->id;
        $storageFolder = 'sprzety/' . $folder;

        // 🖼️ MINIATURA
        if ($request->hasFile('thumbnail')) {
            // usuń starą miniaturę
            if ($equipment->thumbnail && Storage::disk('public')->exists(Str::after($equipment->thumbnail, 'storage/'))) {
                Storage::disk('public')->delete(Str::after($equipment->thumbnail, 'storage/'));
            }

            // zapisz nową miniaturę
            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->storeAs($storageFolder, 'glowne.webp', 'public');
            $equipment->thumbnail = 'storage/' . $thumbnailPath;
        }

        // DODATKOWE ZDJĘCIA
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $photo->storeAs($storageFolder, 'photo_' . time() . '_' . $index . '.webp', 'public');
            }
        }

        $equipment->name = $validated['name'];
        $equipment->description = $validated['description'];
        $equipment->availability = $validated['availability'];
        $equipment->rental_price = $validated['rental_price'];
        $equipment->technical_state = $validated['technical_state'];
        $equipment->category = $validated['category'];
        $equipment->folder_photos = 'storage/' . $storageFolder . '/';

        // PROMOCJE tylko jeśli NIE jest "kategoria"
        if ($equipment->promotion_type !== 'kategoria') {
            $equipment->promotion_type = $validated['promotion_type'];
            $equipment->discount = $validated['discount'];
            $equipment->start_datetime = $validated['start_datetime'];
            $equipment->end_datetime = $validated['end_datetime'];
        }

        $equipment->save();

        return redirect()->route('admin.equipment.index')->with('success', 'Sprzęt został zaktualizowany.');
    }
}
