@extends('layouts.admin')
@section('title','Pesanan / POS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 mb-0">Pesanan</h1>
  <form class="form-inline" method="get">
    <label class="mr-2">Status</label>
    <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
      <option value="">Semua</option>
      @foreach(['open','pending','paid','preparing','ready','served','completed','cancelled'] as $st)
        <option value="{{ $st }}" @if(request('status')===$st) selected @endif>{{ $st }}</option>
      @endforeach
    </select>
  </form>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead class="thead-light">
        <tr><th>Kode</th><th>Meja</th><th>Tipe</th><th>Status</th><th>Total</th><th>Metode</th><th>Waktu</th><th class="text-right pr-3">Aksi</th></tr>
      </thead>
      <tbody>
        @foreach($orders as $o)
            @php
      $tableCode = $o->tableSession->table->code ?? '-';
    @endphp
          <tr>
            <td class="font-weight-bold">{{ $o->code }}</td>
            <td>{{ $tableCode }}</td>
            <td>{{ $o->order_type }}</td>
            <td><span class="badge badge-secondary">{{ $o->status }}</span></td>
            <td>Rp {{ number_format($o->grand_total,0,',','.') }}</td>
            <td>{{ $o->payment_method ?? '-' }}</td>
            <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            <td class="text-right pr-3">
              <a href="{{ route('admin.orders.show',$o) }}" class="btn btn-sm btn-primary">Detail</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $orders->links() }}</div>
</div>
@endsection
