<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ restaurant()->name }} - {{ $order->show_formatted_order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        [dir="rtl"] {
            text-align: right;
        }

        [dir="ltr"] {
            text-align: left;
        }

        body {
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .receipt {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .restaurant-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            display: block;
        }

        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .restaurant-info {
            font-size: 12px;
            margin-bottom: 3px;
            color: #666;
        }

        .order-info {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .order-info h3 {
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .items-table th {
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
        }

        .items-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .qty {
            width: 8%;
            text-align: center;
        }

        .description {
            width: 50%;
        }

        .price {
            width: 20%;
            text-align: right;
        }

        .amount {
            width: 22%;
            text-align: right;
        }

        .modifiers {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        .summary {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
            text-align: right;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }

        .summary-row.secondary {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
            padding-left: 20px;
        }

        .total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
        }

        .qr_code {
            margin: 15px 0;
            text-align: center;
        }

        .qr_code img {
            max-width: 150px;
            height: auto;
        }

        .payment-details {
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .payment-details h4 {
            margin-bottom: 10px;
            color: #333;
        }

        @media print {
            body {
                font-size: 11px;
            }

            .receipt {
                max-width: none;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="header">
            @if ($receiptSettings->show_restaurant_logo)
                <img src="{{ restaurant()->logo_url }}" alt="{{ restaurant()->name }}" class="restaurant-logo">
            @endif
            <div class="restaurant-name">{{ restaurant()->name }}</div>
            <div class="restaurant-info">{{ branch()->address }}</div>
            <div class="restaurant-info">@lang('modules.customer.phone'): {{ restaurant()->phone_number }}</div>
            @if ($receiptSettings->show_tax)
                @foreach ($taxDetails as $taxDetail)
                    <div class="restaurant-info">{{ $taxDetail->tax_name }}: {{ $taxDetail->tax_id }}</div>
                @endforeach
            @endif
        </div>

        <div class="order-info">
            <h3>@lang('modules.settings.orderDetails')</h3>
            <div class="info-grid">
                <div class="info-item">

                    <span>
                        @if(!isOrderPrefixEnabled())
                            <span class="info-label">@lang('modules.order.orderNumber'):</span>
                            <span>#{{ $order->order_number }}</span>
                        @else
                            {{ $order->formatted_order_number }}
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">@lang('app.dateTime'):</span>
                    <span>{{ $order->date_time->timezone(timezone())->translatedFormat('d M Y H:i') }}</span>
                </div>
                @if ($receiptSettings->show_table_number && $order->table && $order->table->table_code)
                    <div class="info-item">
                        <span class="info-label">@lang('modules.settings.tableNumber'):</span>
                        <span>{{ $order->table->table_code }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_total_guest && $order->number_of_pax)
                    <div class="info-item">
                        <span class="info-label">@lang('modules.order.noOfPax'):</span>
                        <span>{{ $order->number_of_pax }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_waiter && $order->waiter && $order->waiter->name)
                    <div class="info-item">
                        <span class="info-label">@lang('modules.order.waiter'):</span>
                        <span>{{ $order->waiter->name }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_customer_name && $order->customer && $order->customer->name)
                    <div class="info-item">
                        <span class="info-label">@lang('modules.customer.customer'):</span>
                        <span>{{ $order->customer->name }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_customer_address && $order->customer && $order->customer->delivery_address)
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="info-label">@lang('modules.customer.customerAddress'):</span>
                        <span>{{ $order->customer->delivery_address }}</span>
                    </div>
                @endif
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="qty">@lang('modules.order.qty')</th>
                    <th class="description">@lang('modules.menu.itemName')</th>
                    <th class="price">@lang('modules.order.price')</th>
                    <th class="amount">@lang('modules.order.amount')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td class="qty">{{ $item->quantity }}</td>
                        <td class="description">
                            <strong>{{ $item->menuItem->item_name }}</strong>
                            @if (isset($item->menuItemVariation))
                                <br><small>({{ $item->menuItemVariation->variation }})</small>
                            @endif
                            @foreach ($item->modifierOptions as $modifier)
                                <div class="modifiers">• {{ $modifier->name }}
                                    @if($modifier->price > 0)
                                        (+{{ currency_format($modifier->price, restaurant()->currency_id) }})
                                    @endif
                                </div>
                            @endforeach
                            @if($item->note)
                                <div class="modifiers"><em>@lang('modules.order.note'): {{ $item->note }}</em></div>
                            @endif
                        </td>
                        <td class="price">{{ currency_format($item->price, restaurant()->currency_id) }}</td>
                        <td class="amount">{{ currency_format($item->amount, restaurant()->currency_id) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <span>@lang('modules.order.subTotal'):</span>
                <span>{{ currency_format($order->sub_total, restaurant()->currency_id) }}</span>
            </div>

            @if (!is_null($order->discount_amount))
                <div class="summary-row">
                    <span>@lang('modules.order.discount')
                        @if ($order->discount_type == 'percent')
                            ({{ rtrim(rtrim($order->discount_value, '0'), '.') }}%)
                        @endif:
                    </span>
                    <span>-{{ currency_format($order->discount_amount, restaurant()->currency_id) }}</span>
                </div>
            @endif

            @foreach ($order->charges as $item)
                <div class="summary-row">
                    <span>{{ $item->charge->charge_name }}
                        @if ($item->charge->charge_type == 'percent')
                            ({{ $item->charge->charge_value }}%)
                        @endif:
                    </span>
                    <span>{{ currency_format(($item->charge->getAmount($order->sub_total - ($order->discount_amount ?? 0))), restaurant()->currency_id) }}</span>
                </div>
            @endforeach

            @if ($order->tip_amount > 0)
                <div class="summary-row">
                    <span>@lang('modules.order.tip'):</span>
                    <span>{{ currency_format($order->tip_amount, restaurant()->currency_id) }}</span>
                </div>
            @endif

            @if ($order->order_type === 'delivery' && !is_null($order->delivery_fee))
                <div class="summary-row">
                    <span>@lang('modules.delivery.deliveryFee'):</span>
                    <span>
                        @if($order->delivery_fee > 0)
                            {{ currency_format($order->delivery_fee, restaurant()->currency_id) }}
                        @else
                            @lang('modules.delivery.freeDelivery')
                        @endif
                    </span>
                </div>
            @endif

            @if ($taxMode == 'order')
                @foreach ($order->taxes as $item)
                    <div class="summary-row">
                        <span>{{ $item->tax->tax_name }} ({{ $item->tax->tax_percent }}%):</span>
                        <span>{{ currency_format(($item->tax->tax_percent / 100) * ($order->sub_total - ($order->discount_amount ?? 0)), restaurant()->currency_id) }}</span>
                    </div>
                @endforeach
            @else
                @if($order->total_tax_amount > 0)
                    @php
                        $taxTotals = [];
                        $totalTax = 0;
                        foreach ($order->items as $item) {
                            $qty = $item->quantity ?? 1;
                            $taxBreakdown = is_array($item->tax_breakup) ? $item->tax_breakup : (json_decode($item->tax_breakup, true) ?? []);
                            foreach ($taxBreakdown as $taxName => $taxInfo) {
                                if (!isset($taxTotals[$taxName])) {
                                    $taxTotals[$taxName] = [
                                        'percent' => $taxInfo['percent'] ?? 0,
                                        'amount' => ($taxInfo['amount'] ?? 0) * $qty
                                    ];
                                } else {
                                    $taxTotals[$taxName]['amount'] += ($taxInfo['amount'] ?? 0) * $qty;
                                }
                            }
                            $totalTax += $item->tax_amount ?? 0;
                        }
                    @endphp
                    @foreach ($taxTotals as $taxName => $taxInfo)
                        <div class="summary-row secondary">
                            <span>{{ $taxName }} ({{ $taxInfo['percent'] }}%)</span>
                            <span>{{ currency_format($taxInfo['amount'], restaurant()->currency_id) }}</span>
                        </div>
                    @endforeach
                    <div class="summary-row">
                        <span>@lang('modules.order.totalTax'):</span>
                        <span>{{ currency_format($totalTax, restaurant()->currency_id) }}</span>
                    </div>
                @endif
            @endif

            @if ($payment)
                <div class="summary-row">
                    <span>@lang('modules.order.balanceReturn'):</span>
                    <span>{{ currency_format($payment->balance, restaurant()->currency_id) }}</span>
                </div>
            @endif

            <div class="summary-row total">
                <span>@lang('modules.order.total'):</span>
                <span>{{ currency_format($order->total, restaurant()->currency_id) }}</span>
            </div>
        </div>

        @if ($receiptSettings->show_payment_details && $order->payments->count())
            <div class="payment-details">
                <h4>@lang('modules.order.paymentDetails')</h4>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="qty">@lang('modules.order.amount')</th>
                            <th class="description">@lang('modules.order.paymentMethod')</th>
                            <th class="price">@lang('app.dateTime')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->payments as $payment)
                            <tr>
                                <td class="qty">{{ currency_format($payment->amount, restaurant()->currency_id) }}</td>
                                <td class="description">@lang('modules.order.' . $payment->payment_method)</td>
                                <td class="price">
                                    @if($payment->payment_method != 'due')
                                        {{ $payment->created_at->timezone(timezone())->translatedFormat('d M Y H:i') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="footer">
            <p>@lang('messages.thankYouVisit')</p>

            @if ($order->status != 'paid' && $receiptSettings->show_payment_qr_code)
                <div class="qr_code">
                    <p>@lang('modules.settings.payFromYourPhone')</p>
                    <img src="{{ $receiptSettings->payment_qr_code_url }}" alt="QR Code">
                    <p>@lang('modules.settings.scanQrCode')</p>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
