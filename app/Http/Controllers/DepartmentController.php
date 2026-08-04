<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount(['doctors', 'schedules', 'registrations'])
            ->orderBy('kode_poli')
            ->get();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_poli'  => ['required', 'string', 'max:10', 'unique:departments,kode_poli', 'regex:/^[A-Z0-9]+$/'],
            'nama_poli'  => ['required', 'string', 'max:100'],
            'deskripsi'  => ['nullable', 'string', 'max:500'],
            'is_active'  => ['boolean'],
        ], [
            'kode_poli.required' => 'Kode poli wajib diisi.',
            'kode_poli.unique'   => 'Kode poli sudah digunakan.',
            'kode_poli.regex'    => 'Kode poli hanya boleh huruf kapital dan angka.',
            'nama_poli.required' => 'Nama poli wajib diisi.',
        ]);

        $validated['kode_poli'] = strtoupper($validated['kode_poli']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Poli ' . $validated['nama_poli'] . ' berhasil ditambahkan.');
    }

    public function show(Department $department)
    {
        $department->load(['doctors.schedules', 'schedules.doctor']);
        $registrasiHariIni = $department->registrations()
            ->whereDate('tanggal_daftar', today())
            ->with('patient')
            ->orderBy('urutan_antrian')
            ->get();

        return view('departments.show', compact('department', 'registrasiHariIni'));
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'kode_poli'  => ['required', 'string', 'max:10', 'unique:departments,kode_poli,' . $department->id, 'regex:/^[A-Z0-9]+$/'],
            'nama_poli'  => ['required', 'string', 'max:100'],
            'deskripsi'  => ['nullable', 'string', 'max:500'],
            'is_active'  => ['boolean'],
        ], [
            'kode_poli.unique' => 'Kode poli sudah digunakan poli lain.',
        ]);

        $validated['kode_poli'] = strtoupper($validated['kode_poli']);
        $validated['is_active'] = $request->boolean('is_active');

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Data poli ' . $department->nama_poli . ' berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        // Cegah hapus poli yang masih memiliki pendaftaran aktif
        $aktif = $department->registrations()
            ->whereIn('status', ['menunggu', 'dipanggil'])
            ->exists();

        if ($aktif) {
            return back()->with('error', 'Poli tidak dapat dihapus karena masih ada antrian aktif.');
        }

        $nama = $department->nama_poli;
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Poli ' . $nama . ' berhasil dihapus.');
    }

    /** Toggle status aktif/nonaktif via AJAX */
    public function toggleActive(Department $department)
    {
        $department->update(['is_active' => !$department->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $department->is_active,
            'message'   => 'Status poli ' . $department->nama_poli . ' diperbarui.',
        ]);
    }
}
