<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('department')
            ->withCount('registrations')
            ->orderBy('nama_dokter')
            ->get();

        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('nama_poli')->get();
        return view('doctors.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'nip'           => ['nullable', 'string', 'max:30', 'unique:doctors,nip'],
            'nama_dokter'   => ['required', 'string', 'max:100'],
            'spesialisasi'  => ['nullable', 'string', 'max:100'],
            'no_telepon'    => ['nullable', 'string', 'max:20'],
            'foto'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_active'     => ['boolean'],
        ], [
            'department_id.required' => 'Poli wajib dipilih.',
            'department_id.exists'   => 'Poli tidak ditemukan.',
            'nama_dokter.required'   => 'Nama dokter wajib diisi.',
            'nip.unique'             => 'NIP sudah digunakan dokter lain.',
            'foto.image'             => 'File foto harus berupa gambar.',
            'foto.max'               => 'Ukuran foto maksimal 2MB.',
        ]);

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('doctors', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Doctor::create($validated);

        return redirect()->route('doctors.index')
            ->with('success', 'Dokter ' . $validated['nama_dokter'] . ' berhasil ditambahkan.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['department', 'schedules', 'registrations.patient']);
        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        $departments = Department::active()->orderBy('nama_poli')->get();
        return view('doctors.edit', compact('doctor', 'departments'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'nip'           => ['nullable', 'string', 'max:30', 'unique:doctors,nip,' . $doctor->id],
            'nama_dokter'   => ['required', 'string', 'max:100'],
            'spesialisasi'  => ['nullable', 'string', 'max:100'],
            'no_telepon'    => ['nullable', 'string', 'max:20'],
            'foto'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_active'     => ['boolean'],
        ]);

        // Ganti foto jika ada upload baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($doctor->foto) {
                Storage::disk('public')->delete($doctor->foto);
            }
            $validated['foto'] = $request->file('foto')->store('doctors', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $doctor->update($validated);

        return redirect()->route('doctors.index')
            ->with('success', 'Data dokter ' . $doctor->nama_dokter . ' berhasil diperbarui.');
    }

    public function destroy(Doctor $doctor)
    {
        // Cegah hapus jika masih ada pendaftaran aktif
        $aktif = $doctor->registrations()
            ->whereIn('status', ['menunggu', 'diperiksa'])
            ->exists();

        if ($aktif) {
            return back()->with('error', 'Dokter tidak dapat dihapus karena masih ada pasien yang menunggu.');
        }

        if ($doctor->foto) {
            Storage::disk('public')->delete($doctor->foto);
        }

        $nama = $doctor->nama_dokter;
        $doctor->delete();

        return redirect()->route('doctors.index')
            ->with('success', 'Dokter ' . $nama . ' berhasil dihapus.');
    }

    /** Toggle status aktif/nonaktif dokter via AJAX */
    public function toggleActive(Doctor $doctor)
    {
        $doctor->update(['is_active' => !$doctor->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $doctor->is_active,
            'message'   => 'Status dokter ' . $doctor->nama_dokter . ' diperbarui.',
        ]);
    }
}
