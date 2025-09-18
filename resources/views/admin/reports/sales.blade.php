@extends('layouts.admin')
@section('title','Laporan Penjualan')
@php
  // Default agar tidak error jika controller belum mengirim variabel ini
  $summary = $summary ?? ['count' => 0, 'total' => 0];
  $breakdownMethods = $breakdownMethods ?? [];
  $breakdownStatus  = $breakdownStatus  ?? [];
@endphp

@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline" method="get">
      <label class="mr-2">Dari</label>
      <input type="date" name="from" class="form-control form-control-sm mr-2" value="{{ request('from') }}">
      <label class="mr-2">Sampai</label>
      <input type="date" name="to" class="form-control form-control-sm mr-2" value="{{ request('to') }}">
      <button class="btn btn-sm btn-primary mr-2">Filter</button>
      <a class="btn btn-sm btn-secondary" href="{{ route('admin.reports.sales.print', request()->all()) }}" target="_blank"><i class="fas fa-print mr-1"></i> Cetak</a>
    </form>
  </div>
  <div class="card-body p-0">
    {{-- letakkan di atas tabel --}}
<div class="row mb-3">
  <div class="col-md-4">
    <div class="card"><div class="card-body py-2">
      <div class="d-flex justify-content-between">
        <div>Total Transaksi</div>
        <div><strong>Rp {{ number_format($summary['total'] ?? 0,0,',','.') }}</strong></div>
      </div>
      <div class="small text-muted">{{ $summary['count'] ?? 0 }} transaksi</div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body py-2">
      <div class="mb-1"><strong>Per Metode</strong></div>
      @if(!empty($breakdownMethods))
      @foreach($breakdownMethods as $m => $r)
        <div class="d-flex justify-content-between small">
          <span>{{ strtoupper($m) }}</span>
          <span>{{ $r['count'] }} trx — Rp {{ number_format($r['amount'],0,',','.') }}</span>
        </div>
      @endforeach
      @endif
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body py-2">
      <div class="mb-1"><strong>Per Status</strong></div>
      @if(!empty($breakdownStatus))
      @foreach($breakdownStatus as $s => $r)
        <div class="d-flex justify-content-between small">
          <span>{{ $s }}</span>
          <span>{{ $r['count'] }} trx — Rp {{ number_format($r['amount'],0,',','.') }}</span>
        </div>
      @endforeach
        @endif
    </div></div>
  </div>
</div>

    <table class="table table-striped mb-0">
      <thead class="thead-light"><tr><th>Kode</th><th>Status</th><th>Metode</th><th>Total</th><th>Tanggal</th></tr></thead>
      <tbody>
        @foreach($rows as $o)
          <tr>
            <td>{{ $o->code }}</td>
            <td>{{ $o->status }}</td>
            <td>{{ $o->payment_method ?? '-' }}</td>
            <td>Rp {{ number_format($o->grand_total,0,',','.') }}</td>
            <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="font-weight-bold">
          <td colspan="3" class="text-right">Total Transaksi</td>
          <td colspan="2">Rp {{ number_format($summary['total'] ?? 0,0,',','.') }} ({{ $summary['count'] ?? 0 }} trx)</td>
        </tr>
      </tfoot>
    </table>
  </div>
  <div class="card-footer">{{ $rows->links() }}</div>
</div>
@endsection
