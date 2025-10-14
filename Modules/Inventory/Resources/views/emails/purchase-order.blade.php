<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ trans('inventory::modules.purchaseOrder.purchase_order') }} - {{ $purchaseOrder->po_number }}</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --background-subtle: #f9fafb;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.5;
            color: var(--text-primary);
            background-color: #ffffff;
            -webkit-font-smoothing: antialiased;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .po-number {
            font-size: 15px;
            color: var(--text-secondary);
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 12px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 32px;
            margin-bottom: 40px;
        }

        .details-section h3 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .details-section p {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .details-section strong {
            color: var(--text-primary);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            font-size: 14px;
        }

        th {
            background-color: var(--background-subtle);
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.05em;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .amount {
            font-family: 'SF Mono', SFMono-Regular, ui-monospace, monospace;
            text-align: right;
        }

        .total-row {
            background-color: var(--background-subtle);
            font-weight: 600;
        }

        .notes {
            background-color: var(--background-subtle);
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 40px;
        }

        .notes h4 {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .notes p {
            font-size: 14px;
            white-space: pre-line;
        }

        .footer {
            text-align: center;
            color: var(--text-secondary);
            font-size: 13px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }

        .meta-info {
            color: var(--text-secondary);
            font-size: 12px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ trans('inventory::modules.purchaseOrder.purchase_order') }}</div>
            <div class="po-number">{{ $purchaseOrder->po_number }}</div>
        </div>

        <div class="details-grid">
            <div class="details-section">
                <h3>{{ trans('inventory::modules.supplier.supplierInformation') }}</h3>
                <p><strong>{{ $purchaseOrder->supplier->name }}</strong></p>
                <p>{{ $purchaseOrder->supplier->email }}</p>
                <p>{{ $purchaseOrder->supplier->phone }}</p>
                <p>{{ $purchaseOrder->supplier->address }}</p>
            </div>

            <div class="details-section">
                <h3>{{ trans('inventory::modules.purchaseOrder.create_title') }}</h3>
                <p><strong>{{ trans('inventory::modules.purchaseOrder.order_date') }}</strong><br>
                    {{ $purchaseOrder->order_date->translatedFormat('M d, Y') }}</p>
                <p><strong>{{ trans('inventory::modules.purchaseOrder.expected_delivery_date') }}</strong><br>
                    {{ $purchaseOrder->expected_delivery_date?->translatedFormat('M d, Y') ?? '-' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('inventory::modules.inventoryItem.name') }}</th>
                    <th>{{ trans('inventory::modules.purchaseOrder.quantity') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $item->inventoryItem->name }}
                    </td>
                    <td>{{ number_format($item->quantity, 2) }} 
                        <span style="color: var(--text-secondary); font-size: 0.875em;">
                            ({{ $item->inventoryItem->unit->symbol }})
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
           
        </table>

        @if($purchaseOrder->notes)
        <div class="notes">
            <h4>{{ trans('inventory::modules.purchaseOrder.notes') }}</h4>
            <p>{{ $purchaseOrder->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <div class="meta-info">
                {{ trans('inventory::modules.purchaseOrder.created_at') }}: 
                {{ $purchaseOrder->created_at->timezone(timezone())->translatedFormat('M d, Y H:i') }}
            </div>
        </div>
    </div>
</body>
</html>