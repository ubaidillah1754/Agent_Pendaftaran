<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class MerchandiseController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(Request $request)
    {
        $query = Merchandise::query()->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $merchandises = $query->paginate(15)->withQueryString();

        return view('admin.merchandises.index', compact('merchandises'));
    }

    public function create()
    {
        return view('admin.merchandises.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:150'],
            'points_required' => ['required', 'integer', 'min:1'],
            'stock'           => ['required', 'integer', 'min:0'],
            'is_active'       => ['required', 'boolean'],
            'description'     => ['nullable', 'string', 'max:1000'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required'            => 'Nama merchandise wajib diisi.',
            'points_required.required' => 'Jumlah poin yang dibutuhkan wajib diisi.',
            'points_required.min'      => 'Jumlah poin minimal 1.',
            'stock.required'           => 'Jumlah stok awal wajib diisi.',
            'stock.min'                => 'Stok tidak boleh bernilai negatif.',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/merchandise');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            $file->move($destination, $imageName);
        }

        $merchandise = Merchandise::create([
            'name'            => $validated['name'],
            'points_required' => $validated['points_required'],
            'stock'           => $validated['stock'],
            'is_active'       => (bool) $validated['is_active'],
            'description'     => $validated['description'] ?? null,
            'image'           => $imageName,
        ]);

        $this->auditService->log(
            actor: Auth::user(),
            action: 'merchandise_created',
            target: $merchandise,
            oldValues: null,
            newValues: $merchandise->toArray(),
            description: "Membuat merchandise baru: {$merchandise->name} ({$merchandise->points_required} poin, stok: {$merchandise->stock})"
        );

        return redirect()->route('admin.merchandises.index')
            ->with('success', "Merchandise '{$merchandise->name}' berhasil ditambahkan.");
    }

    public function edit(Merchandise $merchandise)
    {
        return view('admin.merchandises.edit', compact('merchandise'));
    }

    public function update(Request $request, Merchandise $merchandise)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:150'],
            'points_required' => ['required', 'integer', 'min:1'],
            'stock'           => ['required', 'integer', 'min:0'],
            'is_active'       => ['required', 'boolean'],
            'description'     => ['nullable', 'string', 'max:1000'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required'            => 'Nama merchandise wajib diisi.',
            'points_required.required' => 'Jumlah poin yang dibutuhkan wajib diisi.',
            'points_required.min'      => 'Jumlah poin minimal 1.',
            'stock.required'           => 'Jumlah stok wajib diisi.',
            'stock.min'                => 'Stok tidak boleh bernilai negatif.',
        ]);

        $oldValues = $merchandise->toArray();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/merchandise');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            $file->move($destination, $imageName);

            // Hapus gambar lama jika ada
            if ($merchandise->image && file_exists(public_path('images/merchandise/' . $merchandise->image))) {
                @unlink(public_path('images/merchandise/' . $merchandise->image));
            }

            $merchandise->image = $imageName;
        }

        $merchandise->name            = $validated['name'];
        $merchandise->points_required = $validated['points_required'];
        $merchandise->stock           = $validated['stock'];
        $merchandise->is_active       = (bool) $validated['is_active'];
        $merchandise->description     = $validated['description'] ?? null;
        $merchandise->save();

        $this->auditService->log(
            actor: Auth::user(),
            action: 'merchandise_updated',
            target: $merchandise,
            oldValues: $oldValues,
            newValues: $merchandise->toArray(),
            description: "Memperbarui merchandise: {$merchandise->name} (Harga: {$merchandise->points_required} poin, Stok: {$merchandise->stock})"
        );

        return redirect()->route('admin.merchandises.index')
            ->with('success', "Merchandise '{$merchandise->name}' berhasil diperbarui.");
    }

    public function destroy(Merchandise $merchandise)
    {
        $name = $merchandise->name;
        $merchandise->delete(); // Soft delete

        $this->auditService->log(
            actor: Auth::user(),
            action: 'merchandise_deleted',
            target: $merchandise,
            oldValues: ['name' => $name],
            newValues: null,
            description: "Menghapus (soft delete) merchandise: {$name}"
        );

        return redirect()->route('admin.merchandises.index')
            ->with('success', "Merchandise '{$name}' berhasil dihapus.");
    }
}
