<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body {
            color: #292524;
            font-family:
                DejaVu Sans,
                sans-serif;
        }

        h1 {
            font-size: 24px;
        }

        table {
            border-collapse: collapse;
            margin-top: 24px;
            width: 100%;
        }

        th,
        td {
            border-bottom: 1px solid #d6d3d1;
            padding: 10px;
            text-align: left;
        }

        th {
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        tfoot td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>HotelHub</h1>
    <p>Invoice {{ $invoice->invoice_number }}</p>
    <p>Reservation {{ $invoice->reservation->reservation_number }} · Room {{ $invoice->reservation->room->room_number }}</p>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total</td>
                <td>${{ number_format($invoice->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3">Balance</td>
                <td>${{ number_format($invoice->balance, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
