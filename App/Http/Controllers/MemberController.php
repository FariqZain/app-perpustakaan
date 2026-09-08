<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = [
            ['id' => 1, 'nama' => 'Ahmad Fauzi', 'nim' => '2024001', 'email' => 'ahmad.fauzi@example.com', 'nomor_telepon' => '081234567890', 'alamat' => 'Jl. Merdeka No. 10', 'status' => 'aktif'],
            ['id' => 2, 'nama' => 'Siti Aminah', 'nim' => '2024002', 'email' => 'siti.aminah@example.com', 'nomor_telepon' => '081298765432', 'alamat' => 'Jl. Diponegoro No. 5', 'status' => 'aktif'],
            ['id' => 3, 'nama' => 'Budi Santoso', 'nim' => '2024003', 'email' => 'budi.santoso@example.com', 'nomor_telepon' => '082112223333', 'alamat' => 'Jl. Sudirman No. 22', 'status' => 'nonaktif'],
        ];

        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();

        return redirect()->route('members.index')
            ->with('success', "Anggota \"{$validated['nama']}\" berhasil ditambahkan (data dummy, belum tersimpan ke database).");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
