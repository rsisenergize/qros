@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => route('shop_restaurant', ['hash' => $settings->hash])])
{{ $settings->name }}
@endcomponent
@endslot

## {{ __('email.sendOrderBill.dear') }} {{ $order->customer->name }},

{{ __('email.sendOrderBill.thankYouForDining') }} **{{ $settings->name }}**! {{ __('email.sendOrderBill.excitedToServe')}}

## {{ __('email.sendOrderBill.orderSummary') }}

**{{ __('email.sendOrderBill.order') }}**: #{{ $order->show_formatted_order_number }}

**{{ __('email.sendOrderBill.orderType') }}**: {{ ucwords(str_replace('_', ' ', $order->order_type)) }}
@component('mail::table')
| {{ __('modules.menu.itemName') }}           | {{ __('modules.order.qty') }}      | {{ __('modules.order.price') }}     |
|:-------------- |:-------------:| ---------:|
@foreach ($items as $item)
| **{{ $item->menuItem->item_name }}** @if ($item->modifierOptions->isNotEmpty()) @foreach ($item->modifierOptions as $modifier) <br> &nbsp;• {{ $modifier->name }} @if ($modifier->price > 0) (+{{ currency_format($modifier->price, $settings->currency_id) }}) @endif @endforeach @endif @if($item->note) <br> <em>{{ __('modules.order.note') }}: {{ $item->note }}</em> @endif | {{ $item->quantity }} | {{ currency_format(($item->price + $item->modifierOptions->sum('price')) * $item->quantity, $settings->currency_id) }} |
@endforeach
| **{{ __('modules.order.subTotal') }}**   |               | **{{ currency_format($subtotal, $settings->currency_id) }}** |
@if (!is_null($order->discount_amount))
| **{{ __('modules.order.discount') }}** @if ($order->discount_type == 'percent') **({{ rtrim(rtrim($order->discount_value, '0'), '.') }}%)** @endif |     | **-{{ currency_format($order->discount_amount, $settings->currency_id) }}** |
@endif
@if($order->tip_amount > 0)
| **{{ __('modules.order.tip') }}** |     | **{{ currency_format($order->tip_amount, $settings->currency_id) }}** |
@endif
@if ($order->order_type === 'delivery')
| **{{ __('modules.order.deliveryFee') }}** |     | @if($order->delivery_fee > 0) **{{ currency_format($order->delivery_fee, $settings->currency_id) }}** @else **<span style="color: #10B981">{{ __('modules.delivery.freeDelivery') }}</span>** @endif |
@endif
@foreach ($chargesWithAmount as $charge)
| **{{ $charge['name'] }}** @if ($charge['type'] == 'percent') **({{ rtrim(rtrim($charge['rate'], '0'), '.') }}%)** @endif |     | **{{ currency_format($charge['amount'], $settings->currency_id) }}** |
@endforeach
@if ($taxMode == 'order')
@foreach ($taxesWithAmount as $tax)
| **{{ $tax['name'] }} ({{ $tax['rate'] }}%)** |     | **{{ currency_format($tax['amount'], $settings->currency_id) }}** |
@endforeach
@else
@php
    $taxTotals = [];
    $totalTax = 0;
    foreach ($items as $item) {
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
| {{ $taxName }} ({{ $taxInfo['percent'] }}%) |     | {{ currency_format($taxInfo['amount'], $settings->currency_id) }} |
@endforeach
| **{{ __('modules.order.totalTax') }}** |     | **{{ currency_format($totalTax, $settings->currency_id) }}** |
@endif
| **{{ __('modules.order.total') }}**      |               | **{{ currency_format($totalPrice, $settings->currency_id) }}** |
@endcomponent

**{{ __('app.date') }}**: {{ $order->date_time->timezone($settings->timezone)->translatedFormat('F j, Y, g:i a') }}

{{ __('email.sendOrderBill.satisfactionMessage') }}

@component('mail::button', ['url' => route('orders.pdf', $order->id)])
{{ __('email.sendOrderBill.downloadReceipt') }}
@endcomponent

@lang('app.regards'),<br>
{{ $settings->name }}

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
    © {{ date('Y') }} {{ $settings->name }}. @lang('app.allRightsReserved')
@endcomponent
@endslot
@endcomponent
