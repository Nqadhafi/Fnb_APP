<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Cetak Laporan Transaksi</title>
  <style>
    body { font: 12px/1.4 Arial, sans-serif; }
    h1 { font-size: 16px; margin: 0 0 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 6px; }
    th { background: #f4f4f4; text-align: left; }
    .text-right { text-align: right; }
    .small { color:#666; font-size: 11px; }
    .mt-2 { margin-top: 8px; }
    .mb-0 { margin-bottom: 0; }
  </style>
</head>
<body onload="window.print()">
  <h1>Laporan Transaksi</h1>
  <div class="small">
    Periode:
    {{ $from ? \Illuminate\Support\Carbon::parse($from)->format('d/m/Y') : now()->format('d/m/Y') }}
    — 
    {{ $to ? \Illuminate\Support\Carbon::parse($to)->format('d/m/Y') : now()->format('d/m/Y') }}
    @if($status) | Status: {{ $status }} @endif
    @if($method) | Metode: {{ $method }} @endif
    @if($otype)  | Tipe: {{ $otype }} @endif
  </div>

  <table class="mt-2">
    <thead>
      <tr>
        <th>Kode</th>
        <th>Status</th>
        <th>Metode</th>
        <th>Tipe</th>
        <th class="text-right">Total</th>
        <th>Waktu</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $o)
        <tr>
          <td>{{ $o->code }}</td>
          <td>{{ $o->status }}</td>
          <td>{{ $o->payment_method ?? '-' }}</td>
          <td>{{ $o->order_type }}</td>
          <td class="text-right">Rp {{ number_format($o->grand_total,0,',','.') }}</td>
          <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <th colspan="4" class="text-right">TOTAL</th>
        <th class="text-right">Rp {{ number_format($summary['total'] ?? 0,0,',','.') }}</th>
        <th>{{ $summary['count'] ?? 0 }} trx</th>
      </tr>
    </tfoot>
  </table>
</body>
</html>
