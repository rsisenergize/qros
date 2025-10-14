<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
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
        @page {
            margin: 2.5cm 2cm;
        }
        body {
            font-family: 'NotoSans', sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #2d3748;
        }
        .logo {
            float: left;
            margin-right: 20px;
        }
        .logo img {
            max-height: 80px;
            width: auto;
        }
        .header {
            position: relative;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            float: left;
            margin-top: 10px;
        }
        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 9pt;
            color: #4a5568;
        }
        .document-info {
            float: right;
            text-align: right;
        }
        .document-title {
            font-size: 18pt;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 10px;
        }
        .document-number {
            font-size: 11pt;
            color: #4a5568;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .section {
            margin-bottom: 30px;
        }
        .grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .box {
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8fafc;
        }
        .label {
            font-size: 9pt;
            color: #4a5568;
            margin-bottom: 5px;
        }
        .value {
            font-size: 10pt;
            color: #1a202c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px;
            font-size: 9pt;
            font-weight: bold;
            color: #4a5568;
            text-align: left;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9pt;
            color: #2d3748;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 9pt;
            font-weight: bold;
        }
        .status-draft { background-color: #edf2f7; color: #2d3748; }
        .status-sent { background-color: #ebf8ff; color: #2b6cb0; }
        .status-received { background-color: #f0fff4; color: #2f855a; }
        .status-partially { background-color: #fffff0; color: #975a16; }
        .status-cancelled { background-color: #fff5f5; color: #c53030; }
        
        .footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #718096;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .page-number:after {
            content: counter(page);
        }

        .order-total {
            font-weight: bold;
            font-size: 12pt;
        }

    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="logo">
            <img src="{{ restaurant()->logo_url }}"  style="max-height: 80px; width: auto;">
        </div>
        <div class="company-info">
            <div class="company-name">{{ restaurant()->name }}</div>
            <div class="company-details">
                {{ branch()->name }}<br>
                {{ branch()->address }}<br>
                {{ branch()->phone }}
            </div>
        </div>
        <div class="document-info">
            <div class="document-title">{{ trans('inventory::modules.purchaseOrder.purchase_order') }}</div>
            <div class="document-number">{{ $purchaseOrder->po_number }}</div>
            <div class="status-badge status-{{ $purchaseOrder->status }}">
                {{ trans('inventory::modules.purchaseOrder.status.' . $purchaseOrder->status) }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="grid">
            <div class="col">
                <div class="box">
                    <div class="label">{{ trans('inventory::modules.purchaseOrder.supplier') }}</div>
                    <div class="value">{{ $purchaseOrder->supplier->name }}</div>
                    <div style="margin-top: 10px;">
                        {{ $purchaseOrder->supplier->address }}<br>
                        {{ $purchaseOrder->supplier->phone }}<br>
                        {{ $purchaseOrder->supplier->email }}
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="box">
                    <div class="grid" style="margin-bottom: 10px;">
                        <div class="col" style="padding: 0;">
                            <div class="label">{{ trans('inventory::modules.purchaseOrder.order_date') }}</div>
                            <div class="value">{{ $purchaseOrder->order_date->format('M d, Y') }}</div>
                        </div>
                        <div class="col" style="padding: 0;">
                            <div class="label">{{ trans('inventory::modules.purchaseOrder.expected_delivery_date') }}</div>
                            <div class="value">{{ $purchaseOrder->expected_delivery_date?->format('M d, Y') ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="grid" style="margin-bottom: 0;">
                        <div class="col" style="padding: 0;">
                            <div class="label">{{ trans('inventory::modules.purchaseOrder.created_at') }}</div>
                            <div class="value">{{ $purchaseOrder->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">{{ trans('inventory::modules.inventoryItem.name') }}</th>
                    <th style="width: 15%;">{{ trans('inventory::modules.purchaseOrder.ordered_quantity') }}</th>
                    <th style="width: 15%;">{{ trans('inventory::modules.purchaseOrder.received_quantity') }}</th>
                    <th style="width: 15%;">{{ trans('inventory::modules.purchaseOrder.unit_price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div>{{ $item->inventoryItem->name }}
                                <span style="font-size: 8pt;">
                                    ({{ $item->inventoryItem->unit->symbol }})
                                </span>
                            </div>
                            @if($item->inventoryItem->category)
                                <div style="color: #718096; font-size: 8pt;">
                                    {{ trans('inventory::modules.inventoryItem.category') }}: {{ $item->inventoryItem->category->name }}
                                </div>
                            @endif
                        </td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ number_format($item->received_quantity, 2) }}</td>
                        <td>{{ currency_format($item->unit_price, restaurant()->currency_id) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="order-total" style="text-align: right;">{{ trans('modules.billing.total') }}</td>
                    <td colspan="1" class="order-total">{{ currency_format($purchaseOrder->total_amount, restaurant()->currency_id) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($purchaseOrder->notes)
        <div class="section">
            <div class="box">
                <div class="label">{{ trans('inventory::modules.purchaseOrder.notes') }}</div>
                <div style="white-space: pre-line; font-size: 8pt;">{{ $purchaseOrder->notes }}</div>
            </div>
        </div>
    @endif

    <div class="footer">
        <div>Generated on {{ now(timezone())->format('F d, Y \a\t H:i:s') }}</div>
    </div>
</body>
</html> 