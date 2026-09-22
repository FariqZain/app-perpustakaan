<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Anggota</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: #f4f4f4; }
        .alert-success { background: #dcfce7; color: #166534; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .btn { padding: 6px 12px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn-sm { padding: 4px 8px; font-size: 13px; }
        .btn-danger { background: #dc2626; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
        .search-box { margin-bottom: 15px; display: flex; gap: 8px; }
        .search-box input[type="text"] { padding: 6px; width: 250px; }
    </style>
</head>
<body>
    <h1>Daftar Anggota</h1>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <form action="{{ route('members.index') }}" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Cari nama anggota..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm">Cari</button>
            @if(request('search'))
                <a href="{{ route('members.index') }}" class="btn btn-sm" style="background: #6b7280;">Reset</a>
            @endif
        </form>

        <a href="{{ route('members.create') }}" class="btn">+ Tambah Anggota</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $index => $member)
                <tr>
                    <td>{{ $members->firstItem() + $index }}</td>
                    <td>{{ $member['nama'] }}</td>
                    <td>{{ $member['nim'] }}</td>
                    <td>{{ $member['email'] }}</td>
                    <td>{{ $member['nomor_telepon'] }}</td>
                    <td>{{ ucfirst($member['status']) }}</td>
                    <td style="display: flex; gap: 6px;">
                        <a href="{{ route('members.show', $member['id']) }}" class="btn btn-sm" style="background: #0284c7;">Detail</a>
                        <a href="{{ route('members.edit', $member['id']) }}" class="btn btn-sm" style="background: #eab308;">Edit</a>
                        <form action="{{ route('members.destroy', $member['id']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data anggota ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $members->appends(request()->query())->links() }}
    </div>
</body>
</html>