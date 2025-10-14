<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order #{{ $purchaseOrder->po_number }}</title>
    <style>
        @font-face {
            font-family: 'NotoSans';
            src: url('{{ public_path('fonts/NotoSans-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'NotoSans';
            src: url('{{ public_path('fonts/NotoSans-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        body {
            font-family: 'NotoSans', sans-serif;
            font-size: 14px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .po-details {
            margin-bottom: 30px;
        }
        .po-details table {
            width: 100%;
        }
        .po-details td {
            padding: 5px;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
        }
        .total {
            text-align: right;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="restaurant-name">{{ $purchaseOrder->branch->restaurant->name }}</div>
        <div>{{ $purchaseOrder->branch->name }}</div>
        <div>{{ $purchaseOrder->branch->address }}</div>
    </div>

    <h1 style="text-align: center;">PURCHASE ORDER</h1>

    <div class="po-details">
        <table>
            <tr>
                <td width="50%">
                    <strong>Supplier:</strong><br>
                    {{ $purchaseOrder->supplier->name }}<br>
                    {{ $purchaseOrder->supplier->address }}<br>
                    Phone: {{ $purchaseOrder->supplier->phone }}<br>
                    Email: {{ $purchaseOrder->supplier->email }}
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>PO Number:</strong> {{ $purchaseOrder->po_number }}<br>
                    <strong>Order Date:</strong> {{ $purchaseOrder->order_date->format('M d, Y') }}<br>
                    <strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date?->format('M d, Y') ?? 'Not specified' }}<br>
                    <strong>Status:</strong> {{ ucfirst($purchaseOrder->status) }}
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->inventoryItem->name }}</td>
                    <td>{{ $item->inventoryItem->unit->symbol }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;"><strong>Total Amount:</strong></td>
                <td><strong>{{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($purchaseOrder->notes)
        <div style="margin-bottom: 30px;">
            <strong>Notes:</strong><br>
            {{ $purchaseOrder->notes }}
        </div>
    @endif

    <div style="margin-top: 50px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 33%; text-align: center;">
                    _______________________<br>
                    Prepared by<br>
                    {{ $purchaseOrder->createdBy->name }}
                </td>
                <td style="width: 33%; text-align: center;">
                    _______________________<br>
                    Approved by
                </td>
                <td style="width: 33%; text-align: center;">
                    _______________________<br>
                    Received by
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html> 