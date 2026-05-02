<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->invoice_number }}</title>

    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            background: #f4f6f8;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 850px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            color: #4CAF50;
        }

        .company {
            text-align: right;
            font-size: 12px;
            color: #666;
        }

        .grid {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            flex: 1;
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #4CAF50;
        }

        .info {
            font-size: 13px;
            margin-bottom: 6px;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #4CAF50;
            color: white;
            padding: 10px;
            font-size: 13px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .text-right {
            text-align: right;
        }

        .total {
            background: #f1f5f9;
            font-weight: bold;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: bold;
        }

        .paid { background: #d4edda; color: #155724; }
        .pending { background: #fff3cd; color: #856404; }
        .cancelled { background: #f8d7da; color: #721c24; }
        .free { background: #d1ecf1; color: #0c5460; }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>

<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div>
            <div class="title">INVOICE</div>
            <div style="font-size:12px;color:#666;">
                #{{ $order->invoice_number }}
            </div>
        </div>

        <div class="company">
            SH3 Event Management<br>
            Indonesia<br>
            support@sh3event.com
        </div>
    </div>

    <!-- CUSTOMER & INVOICE -->
    <div class="grid">
        <div class="card">
            <div class="section-title">Customer</div>
            <div class="info"><span class="label">Name:</span> {{ $order->participant->name ?? '-' }}</div>
            <div class="info"><span class="label">Email:</span> {{ $order->participant->email ?? '-' }}</div>
            <div class="info"><span class="label">Phone:</span> {{ $order->participant->phone ?? '-' }}</div>
            <div class="info"><span class="label">Gender:</span> {{ ucfirst($order->participant->gender ?? '-') }}</div>
        </div>

        <div class="card">
            <div class="section-title">Invoice Info</div>
            <div class="info"><span class="label">Date:</span> {{ $order->created_at->format('d M Y H:i') }}</div>
            <div class="info"><span class="label">Ticket:</span> {{ $order->ticket_code }}</div>
            <div class="info">
                <span class="label">Status:</span>
                <span class="badge
                    @if($order->status=='paid') paid
                    @elseif($order->status=='pending') pending
                    @elseif($order->status=='cancelled') cancelled
                    @else free
                    @endif">
                    {{ strtoupper($order->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- EVENT -->
    <div class="card">
        <div class="section-title">Event Details</div>
        <div class="info"><span class="label">Title:</span> {{ $order->event->title ?? '-' }}</div>
        <div class="info">
            <span class="label">Date:</span>
            {{ optional($order->event->start_date)->format('d M Y H:i') }} -
            {{ optional($order->event->end_date)->format('d M Y H:i') }}
        </div>
        <div class="info"><span class="label">Location:</span> {{ $order->event->location ?? '-' }}</div>
        <div class="info"><span class="label">Category:</span> {{ $order->event->category->name ?? '-' }}</div>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <strong>{{ $order->event->title ?? 'Event Ticket' }}</strong><br>
                <small>Ticket: {{ $order->ticket_code }}</small>
            </td>
            <td class="text-right">1</td>
            <td class="text-right">
                {{ $order->total_price > 0 ? 'Rp '.number_format($order->total_price,0,',','.') : 'FREE' }}
            </td>
            <td class="text-right">
                {{ $order->total_price > 0 ? 'Rp '.number_format($order->total_price,0,',','.') : 'FREE' }}
            </td>
        </tr>

        <tr class="total">
            <td colspan="3" class="text-right">TOTAL</td>
            <td class="text-right">
                {{ $order->total_price > 0 ? 'Rp '.number_format($order->total_price,0,',','.') : 'FREE' }}
            </td>
        </tr>
        </tbody>
    </table>

    <!-- PAYMENT -->
    @if($order->payment)
    <div class="card" style="margin-top:20px; background:#e8f5e9;">
        <div class="section-title">Payment</div>
        <div class="info"><span class="label">Method:</span> {{ $order->payment->payment_method }}</div>
        <div class="info"><span class="label">Amount:</span> Rp {{ number_format($order->payment->amount,0,',','.') }}</div>
        <div class="info"><span class="label">Paid At:</span> {{ \Carbon\Carbon::parse($order->payment->paid_at)->format('d M Y H:i') }}</div>
    </div>
    @endif

    <!-- INSTRUCTION -->
    @if($order->status == 'pending' && $order->total_price > 0)
    <div class="card" style="margin-top:20px; background:#fff3cd;">
        <div class="section-title" style="color:#856404;">Payment Instruction</div>
        <p>Transfer to:</p>
        <p><strong>BCA</strong> - 1234567890</p>
        <p><strong>Mandiri</strong> - 0987654321</p>
    </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <p><strong>Thank you for your purchase 🎉</strong></p>
        <p>This invoice is generated automatically</p>
    </div>

</div>
</body>
</html>