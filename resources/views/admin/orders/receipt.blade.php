<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Struk {{ $order->receipt_no }}</title>
<style>
  body { font: 11px/1.3 Arial, sans-serif; }
  .text-right { text-align:right }
  .text-center { text-align:center }
  table { width:100%; border-collapse:collapse }
  td,th { padding:2px }
  hr { border:0; border-top:1px dashed #000; margin:4px 0 }
</style>
</head>
<body onload="window.print()">
  <div class="text-center">
    <h3 style="margin:0">{{ config('app.name') }}</h3>
    <div>{{ config('app.address') ?? 'Alamat Usaha' }}</div>
    <div>Telp: {{ config('app.phone') ?? '-' }}</div>
    <hr>
    <div>No: {{ $order->receipt_no }} | {{ $order->created_at->format('d/m/Y H:i') }}</div>
    <div>Pelanggan: {{ $order->user->name ?? '-' }}</div>
    @if($order->tableSession && $order->tableSession->table)
      <div>Meja: {{ $order->tableSession->table->code }}</div>
    @endif
    <hr>
  </div>

  <table>
    <thead>
      <tr>
        <th>Item</th>
        <th class="text-center">Qty</th>
        <th class="text-right">Harga</th>
        <th class="text-right">Sub</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $i)
      <tr>
        <td>{{ $i->product_name }}</td>
        <td class="text-center">{{ $i->qty }}</td>
        <td class="text-right">{{ number_format($i->unit_price,0,',','.') }}</td>
        <td class="text-right">{{ number_format($i->line_total,0,',','.') }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr><th colspan="3" class="text-right">Subtotal</th><th class="text-right">{{ number_format($order->subtotal,0,',','.') }}</th></tr>
      @if($order->service_charge>0)
        <tr><th colspan="3" class="text-right">Service</th><th class="text-right">{{ number_format($order->service_charge,0,',','.') }}</th></tr>
      @endif
      @if($order->tax_total>0)
        <tr><th colspan="3" class="text-right">Pajak</th><th class="text-right">{{ number_format($order->tax_total,0,',','.') }}</th></tr>
      @endif
      <tr><th colspan="3" class="text-right">TOTAL</th><th class="text-right">{{ number_format($order->grand_total,0,',','.') }}</th></tr>
      <tr><th colspan="3" class="text-right">Bayar</th><th class="text-right">{{ strtoupper($order->payment_method ?? '-') }}</th></tr>
    </tfoot>
  </table>

  <hr>
  <div class="text-center">
    <p>Terima kasih atas kunjungan Anda 🙏</p>
    <div class="small">Barang yang sudah dibeli tidak dapat dikembalikan.</div>
  </div>
</body>
</html>
