<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Participant Detail - {{ $participant->hash_id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background: #f5f5f5;
            padding: 10px;
            border-left: 4px solid #4CAF50;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-inactive { background: #f8d7da; color: #721c24; }
        .badge-male { background: #cce5ff; color: #004085; }
        .badge-female { background: #ffe5e5; color: #c62828; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f5f5f5;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PARTICIPANT DETAIL REPORT</h1>
        <p>Generated on {{ date('d F Y H:i:s') }}</p>
    </div>

    <div class="section">
        <div class="section-title">
            <strong>Personal Information</strong>
        </div>
        <div class="info-row">
            <div class="info-label">Hash ID</div>
            <div class="info-value"><strong>{{ $participant->hash_id }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Full Name</div>
            <div class="info-value">{{ $participant->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email</div>
            <div class="info-value">{{ $participant->email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Phone</div>
            <div class="info-value">{{ $participant->phone }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Gender</div>
            <div class="info-value">
                <span class="badge badge-{{ $participant->gender }}">{{ ucfirst($participant->gender) }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Birthdate</div>
            <div class="info-value">{{ $participant->birthdate->format('d F Y') }} ({{ $participant->birthdate->age }} years)</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status</div>
            <div class="info-value">
                <span class="badge badge-{{ $participant->status }}">{{ ucfirst($participant->status) }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Registered Date</div>
            <div class="info-value">{{ $participant->created_at->format('d F Y H:i:s') }}</div>
        </div>
        @if($participant->notes)
        <div class="info-row">
            <div class="info-label">Notes</div>
            <div class="info-value">{{ $participant->notes }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">
            <strong>Order History</strong>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Event</th>
                    <th>Ticket Code</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participant->orders as $order)
                <tr>
                    <td>{{ $order->invoice_number }}</td>
                    <td>{{ $order->event->title ?? 'N/A' }}</td>
                    <td>{{ $order->ticket_code }}</td>
                    <td>@if($order->total_price > 0) Rp {{ number_format($order->total_price, 0, ',', '.') }} @else FREE @endif</td>
                    <td>
                        @if($order->status == 'paid') Paid
                        @elseif($order->status == 'pending') Pending
                        @elseif($order->status == 'free') Free
                        @else Cancelled @endif
                    </td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No orders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This is an official document generated by SH3 Event Management System</p>
    </div>
</body>
</html>