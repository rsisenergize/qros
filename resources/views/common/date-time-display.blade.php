@php
    $now = \Carbon\Carbon::now(timezone());
    $color = 'text-gray-500';
    $isToday = false;

    $date = $date->setTimezone(timezone());

    if ($date->isToday()) {
        $color = 'text-green-600';
        $isToday = true;
    } elseif ($date->isYesterday()) {
        $color = 'text-blue-800';
    }

    // Format date - hide year if it's current year and add ordinal suffix
    $day = $date->translatedFormat('j'); // Day without leading zero
    $month = $date->translatedFormat('M');
    $year = $date->translatedFormat('Y');

    $time = $date->translatedFormat('h:i A');



    // Add ordinal suffix
    $ordinal = '';
    if ($day >= 11 && $day <= 13) {
        $ordinal = 'th';
    } else {
        switch ($day % 10) {
            case 1: $ordinal = 'st'; break;
            case 2: $ordinal = 'nd'; break;
            case 3: $ordinal = 'rd'; break;
            default: $ordinal = 'th'; break;
        }
    }

    $dateFormat = $date?->year === $now->year ? "{$day}<sup>{$ordinal}</sup> {$month}, {$time}" : "{$day}<sup>{$ordinal}</sup> {$month} {$year} {$time}";
@endphp

@if($date)
    @if(!$isToday)
        <span class="{{ $color }} text-xs">{!! $dateFormat !!} </span>
    @endif
    @if($isToday)
        <span class="{{ $color }} text-xs">{{ $time }}</span>
    @endif
    <p class="text-[11px] text-gray-400">{{ $date?->diffForHumans(short:true) }}</p>
@else
    -
@endif
