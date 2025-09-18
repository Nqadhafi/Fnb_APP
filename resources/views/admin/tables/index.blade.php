@extends('layouts.admin')
@section('title','Meja')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 mb-0">Meja</h1>
  <a href="{{ route('admin.tables.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead class="thead-light"><tr><th>Kode</th><th>Nama</th><th>Kapasitas</th><th>Status</th><th class="text-right pr-3">Aksi</th></tr></thead>
      <tbody>
        @foreach($rows as $t)
          <tr>
            <td class="font-weight-bold">{{ $t->code }}</td>
            <td>{{ $t->name ?? '-' }}</td>
            <td>{{ $t->capacity }}</td>
            <td><span class="badge badge-{{ $t->status==='available'?'success':($t->status==='occupied'?'warning':'secondary') }}">{{ $t->status }}</span></td>
            <td class="text-right pr-3">
              <a href="{{ route('admin.tables.edit',$t) }}" class="btn btn-sm btn-outline-primary">Edit</a>
              <form action="{{ route('admin.tables.destroy',$t) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus meja?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>

              {{-- Buka/Tutup sesi cepat --}}
              @php
                $active = \App\Models\TableSession::where('dining_table_id',$t->id)->whereNull('closed_at')->first();
              @endphp
              @if(!$active)
                <form action="{{ route('admin.tables.sessions.open',$t) }}" method="post" class="d-inline">@csrf
                  <input type="hidden" name="guest_count" value="1">
                  <button class="btn btn-sm btn-success">Buka Sesi</button>
                </form>
              @else
                <form action="{{ route('admin.tables.sessions.close',$active) }}" method="post" class="d-inline">@csrf
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Tutup sesi meja?')">Tutup Sesi</button>
                </form>
              @endif

            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $rows->links() }}</div>
</div>
@endsection
