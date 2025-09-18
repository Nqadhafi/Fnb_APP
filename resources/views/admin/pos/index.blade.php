@extends('layouts.admin')
@section('title','POS Sessions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 mb-0">Sesi Kasir</h1>
  <form method="post" action="{{ route('admin.pos.open') }}" class="form-inline">@csrf
    <input name="counter_name" class="form-control form-control-sm mr-2" placeholder="Counter (opsional)">
    <input name="opening_float" type="number" step="0.01" class="form-control form-control-sm mr-2" placeholder="Modal awal" required>
    <button class="btn btn-sm btn-primary"><i class="fas fa-play mr-1"></i> Buka Sesi</button>
  </form>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead class="thead-light"><tr><th>ID</th><th>Counter</th><th>Dibuka</th><th>Ditutup</th><th>Expected</th><th>Actual</th><th>Selisih</th><th class="text-right pr-3">Aksi</th></tr></thead>
      <tbody>
        @foreach($rows as $s)
          <tr>
            <td>{{ $s->id }}</td>
            <td>{{ $s->counter_name }}</td>
            <td>{{ $s->opened_at?->format('d/m/Y H:i') }}</td>
            <td>{{ $s->closed_at?->format('d/m/Y H:i') ?? '-' }}</td>
            <td>Rp {{ number_format($s->expected_cash,0,',','.') }}</td>
            <td>Rp {{ number_format($s->actual_cash,0,',','.') }}</td>
            <td class="{{ ($s->cash_variance??0)==0?'':'text-danger' }}">Rp {{ number_format($s->cash_variance,0,',','.') }}</td>
            <td class="text-right pr-3">
              @if(!$s->closed_at)
                <form method="post" action="{{ route('admin.pos.close',$s) }}" class="form-inline">@csrf
                  <input name="actual_cash" type="number" step="0.01" class="form-control form-control-sm mr-2" placeholder="Uang fisik" required>
                  <input name="notes" class="form-control form-control-sm mr-2" placeholder="Catatan">
                  <button class="btn btn-sm btn-danger"><i class="fas fa-stop mr-1"></i> Tutup</button>
                </form>
              @else
                <span class="badge badge-secondary">Closed</span>
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
