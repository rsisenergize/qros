<div>

    <x-kot.kot-card :kot='$kot' wire:key='kot-{{ $kot->id . microtime() }}' :kotSettings='$kotSettings' :cancelReasons="$cancelReasons" :showAllKitchens="$showAllKitchens" :showStatusModal="$showStatusModal" :selectedItemId="$selectedItemId" :selectedItemStatus="$selectedItemStatus" />
</div>
