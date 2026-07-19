<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #102a43;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #102a43;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #f97316;
            text-align: right;
        }
        .info-box {
            background-color: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #102a43;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #102a43;
            padding-top: 10px;
            color: #f97316;
        }
        .status-box {
            background-color: #f3f4f6;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div style="float: left; width: 60%;">
            <img src="{{ public_path('img/logo.jpeg') }}" alt="Logo" style="height: 60px; width: auto; border-radius: 5px; float: left; margin-right: 15px;">
            <div style="overflow: hidden;">
                <div class="company-name">Multi Base Engineering</div>
                <p style="margin: 5px 0 0 0; font-size: 11px;">
                    Ruko Fiorenza, Jl. Raya H. Mirza Cinde Lakoni<br>
                    Jl. Citra Raya Boulevard, Ciakar<br>
                    Kec. Panongan, Kabupaten Tangerang, Banten 15710
                </p>
            </div>
        </div>
        <div style="float: right; width: 45%; text-align: right;">
            <div class="invoice-title">INVOICE</div>
            <p style="margin: 10px 0 0 0;">
                <strong>No:</strong> {{ $order->order_number }}<br>
                <strong>Tanggal:</strong> {{ $order->created_at->format('d F Y') }}
            </p>
        </div>
    </div>

    <div class="info-box">
        <strong>KEPADA:</strong><br>
        <strong>{{ $order->customer->name }}</strong><br>
        {{ $order->customer->email }}<br>
        {{ $order->customer->phone }}
        @if($order->customer_address)
        <br>{{ $order->customer_address }}
        @endif
    </div>

    <strong>DETAIL PRODUK:</strong>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->notes)
                    <br><small style="color: #6b7280;">{{ $item->notes }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($order->shipping_method && $order->shipping_method !== 'pickup')
    <div class="info-box">
        <strong>PENGIRIMAN:</strong><br>
        Metode: 
        @if($order->shipping_method === 'internal')
            Jasa Pribadi (Internal)
        @elseif($order->shipping_method === 'per_km')
            Per Kilometer ({{ $order->distance_km }} km)
        @endif
    </div>
    @endif

    <div class="clearfix">
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></span>
            </div>
            <div class="total-row">
                <span>Biaya Pengiriman:</span>
                <span><strong>Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</strong></span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format(($order->total_price ?? 0) + ($order->shipping_cost ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="status-box" style="clear: both;">
        <table style="width: 100%; margin: 0;">
            <tr>
                <td style="border: none; width: 50%;">
                    <strong>STATUS PEMBAYARAN:</strong><br>
                    @if($order->isFullyPaid())
                        <span style="background-color: #d1fae5; color: #065f46; padding: 5px 10px; border-radius: 5px; display: inline-block; margin-top: 5px;">
                            ✓ LUNAS
                        </span>
                    @else
                        <span style="background-color: #fef3c7; color: #92400e; padding: 5px 10px; border-radius: 5px; display: inline-block; margin-top: 5px;">
                            ⏱ BELUM LUNAS
                        </span>
                    @endif
                </td>
                <td style="border: none; width: 50%; text-align: right;">
                    <strong>STATUS PRODUKSI:</strong><br>
                    <span style="background-color: #e0e7ff; color: #3730a3; padding: 5px 10px; border-radius: 5px; display: inline-block; margin-top: 5px;">
                        {{ strtoupper($order->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    @if($order->payments->where('status', 'verified')->count() > 0)
    <div style="margin-top: 20px;">
        <strong>RIWAYAT PEMBAYARAN:</strong>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->payments->where('status', 'verified') as $payment)
                <tr>
                    <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                    <td class="text-center">Verified</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #6b7280;">
        <p>Terima kasih atas kepercayaan Anda kepada Multi Base Engineering</p>
    </div>
</body>
</html>
