<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $restaurant->name ?? 'Demo Restaurant' }} - @lang('modules.order.kotTicket')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        [dir="rtl"] { text-align: right; }
        [dir="ltr"] { text-align: left; }
        .receipt {
            width: {{ $width - 5 }}mm;
            padding: {{ $thermal ? '1mm' : '6.35mm' }};
            page-break-after: always;
        }
        .header {
            text-align: center;
            margin-bottom: 3mm;
        }
        .bold {
            font-weight: bold;
        }

        .restaurant-info {
            font-size: {{ $width == 56 ? '8pt' : ($width == 80 ? '9pt' : '10pt') }};
            margin-bottom: 1mm;
        }
        .order-info {
            text-align: center;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 2mm 0;
            margin-bottom: 3mm;
            font-size: {{ $width == 56 ? '8pt' : ($width == 80 ? '10pt' : '10pt') }};
        }
        .kot-title {
            font-size: {{ $width == 56 ? '10pt' : ($width == 80 ? '14pt' : '16pt') }};
            font-weight: bold;
            text-align: center;
            margin-bottom: 2mm;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            font-size: {{ $width == 56 ? '8pt' : ($width == 80 ? '10pt' : '10pt') }};
        }
        .items-table th {
            padding: 1mm;
            border-bottom: 1px solid #000;
        }
        [dir="rtl"] .items-table th { text-align: right; }
        [dir="ltr"] .items-table th { text-align: left; }
        .items-table td {
            padding: 1mm 0;
            vertical-align: top;
        }
        .qty {
            width: {{ $width == 56 ? '20%' : ($width == 80 ? '15%' : '12%') }};
            text-align: center;
        }
        .description {
            width: {{ $width == 56 ? '80%' : ($width == 80 ? '85%' : '88%') }};
        }
        .modifiers {
            font-size: {{ $width == 56 ? '6pt' : ($width == 80 ? '8pt' : '9pt') }};
            color: #555;
        }
        .footer {
            text-align: center;
            margin-top: 3mm;
            font-size: {{ $width == 56 ? '7pt' : ($width == 80 ? '9pt' : '10pt') }};
            padding-top: 2mm;
            border-top: 1px dashed #000;
        }
        .italic {
            font-style: italic;
        }
        .order-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            margin-bottom: {{ $width == 56 ? '3px' : '5px' }};
            flex-wrap: nowrap;
        }
        .order-left {
            text-align: left;
            width: 50%;
            flex: {{ $width == 56 ? '1 1 100%' : '1' }};
        }
        .order-right {
            text-align: right;
            width: 50%;
            flex: {{ $width == 56 ? '1 1 100%' : '0 0 auto' }};
            margin-top: {{ $width == 56 ? '3px' : '0' }};
        }
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">

            @if(isset($kotPlace) && $kotPlace)
                <div class="restaurant-info">{{ $kotPlace->name }}</div>
            @endif
        </div>
        <div class="kot-title">
            KOT <span class="bold">#{{ $kot->kot_number }}</span>
        </div>
        <div class="order-info" style="margin-bottom: 3mm;">
            <div class="order-row">
                <!-- Row 1: Order Number (left), Table (right) -->
                <div class="order-left">
                    <span class="bold">
                        {{ $kot->order->show_formatted_order_number }}
                    </span>
                </div>
                <div class="order-right">
                    <span>@lang('modules.table.table'): <span class="bold">{{ $kot->order->table ? $kot->order->table->table_code : '-' }}</span></span>
                </div>
            </div>
            <div class="order-row">
                <!-- Row 2: Date (left), Time (right) -->
                <div class="order-left">
                    @lang('app.date'): {{ $kot->created_at->timezone($kot->branch->restaurant->timezone)->format('d-m-Y') }}
                </div>
                <div class="order-right">
                    @lang('app.time'): {{ $kot->created_at->timezone($kot->branch->restaurant->timezone)->format('h:i A') }}
                </div>
            </div>
            @if($kot->order->waiter)
            <div class="order-row">
                <!-- Row 3: Waiter (left), empty (right) -->
                <div class="order-left">
                    @lang('modules.order.waiter'): <span class="bold">{{ $kot->order->waiter->name }}</span>
                </div>
                <div class="order-right"></div>
            </div>
            @endif
            @if($kot->order->order_type)
            <div class="order-row">
                <!-- Row 4: Order Type (left), Pickup Time if applicable (right) -->
                <div class="order-left">
                    @lang('modules.settings.orderType'): <span class="bold">{{ Str::title(ucwords(str_replace('_', ' ', $kot->order->order_type))) }}</span>
                </div>
                @if($kot->order->order_type === 'pickup' && $kot->order->pickup_date)
                <div class="order-right">

                        @lang('modules.order.pickupAt'): <span class="bold">{{ \Carbon\Carbon::parse($kot->order->pickup_date)->timezone($kot->branch->restaurant->timezone)->format('h:i A') }}</span>

                </div>
                @endif
            </div>
            @endif
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="description">@lang('modules.menu.itemName')</th>
                    <th class="qty">@lang('modules.order.qty')</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = isset($kotPlaceId)
                        ? $kot->items->filter(function($item) use($kotPlaceId) {
                            return $item->menuItem && $item->menuItem->kot_place_id == $kotPlaceId;
                        })
                        : $kot->items;
                @endphp
                @foreach($items as $item)
                    <tr>
                        <td class="description">
                            {{ $item->menuItem->item_name }}
                            @if (isset($item->menuItemVariation))
                                <br><small>({{ $item->menuItemVariation->variation }})</small>
                            @endif
                            @foreach ($item->modifierOptions as $modifier)
                                <div class="modifiers">• {{ $modifier->name }}</div>
                            @endforeach
                            @if ($item->note)
                                <div class="modifiers"><strong>@lang('modules.order.note'):</strong> {{ $item->note }}</div>
                            @endif
                        </td>
                        <td class="qty">{{ $item->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($kot->note)
            <div class="footer">
                <strong>@lang('modules.order.specialInstructions'):</strong>
                <div class="italic">{{$kot->note}}</div>
            </div>
        @endif
    </div>

        <script >
        window.onload = function() {
            // Only call print if not in an iframe
            if (window.self === window.top) {
                window.print();
            }
        }
    </script>


</body>
</html>
