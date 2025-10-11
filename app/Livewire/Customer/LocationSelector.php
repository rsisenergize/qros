<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Collection;
use App\Models\Branch;
use App\Enums\DeliveryFeeType;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class LocationSelector extends Component
{
    use LivewireAlert;

    public ?float $customerLat = 26.9125;
    public ?float $customerLng = 75.7875;
    public ?float $branchLat = null;
    public ?float $branchLng = null;
    public ?float $maxKm = null;
    public ?bool $inRange = null;
    public ?Branch $branch = null;
    public ?string $mapApiKey = null;
    public ?Customer $customer = null;
    public ?Branch $shopBranch = null;
    public $maxPreparationTime = null;
    public $currencyId;

    public ?float $selectedLat = null;
    public ?float $selectedLng = null;
    public ?string $selectedAddress = null;
    public ?Collection $customerAllAddresses = null;
    public bool $showManualLocation = false;

    public ?float $deliveryFee = null;
    public ?string $deliveryMessage = null;
    public ?bool $isFreeDelivery;
    public ?float $orderGrandTotal;
    public $selectedAddressId = null;
    public ?float $distance = null;
    public ?float $freeDeliveryOverAmount = null;

    // New properties for delivery hours
    public ?bool $isDeliveryTimeAvailable = null;
    public ?string $deliveryScheduleStart = null;
    public ?string $deliveryScheduleEnd = null;
    public ?string $restaurantTimezone = null;
    public ?string $nextAvailableTime = null;
    public ?float $etaMin = null;
    public ?float $etaMax = null;
    public $restaurantLogo;

    protected $listeners = [
        'locationSelected' => 'handleLocationSelected',
        'checkDeliveryRange' => 'checkDeliveryRange',
    ];

    public function mount(): void
    {
        $this->branch = Branch::where('id', $this->shopBranch->id)
            ->with(['deliverySetting', 'deliveryFeeTiers', 'restaurant'])
            ->first();

        if ($this->branch) {
            $this->initializeBranchData();
        }

        $this->dispatch('initDeliveryMap', [
            'branchLat' => $this->branchLat,
            'branchLng' => $this->branchLng,
            'maxKm' => $this->maxKm,
            'defaultLat' => $this->customerLat,
            'defaultLng' => $this->customerLng
        ]);
    }

    private function initializeBranchData(): void
    {
        $this->branchLat = $this->branch->lat;
        $this->branchLng = $this->branch->lng;
        $this->customerLat = $this->branchLat;
        $this->customerLng = $this->branchLng;

        $branchDeliverySetting = $this->branch->deliverySetting;
        $this->mapApiKey = global_setting()->google_map_api_key ?? null;
        $this->restaurantTimezone = $this->branch->restaurant?->timezone ?? config('app.timezone');
        $this->restaurantLogo = $this->branch->restaurant?->logo_url;
        $this->customer?->load('addresses');
        $this->customerAllAddresses = $this->customer?->addresses;
        $this->maxKm = $branchDeliverySetting ? $this->convertToKm($branchDeliverySetting->max_radius, $branchDeliverySetting->unit) : 1;
        $this->freeDeliveryOverAmount = $branchDeliverySetting?->free_delivery_over_amount;

        // Initialize delivery schedule data
        $this->deliveryScheduleStart = $branchDeliverySetting?->delivery_schedule_start;
        $this->deliveryScheduleEnd = $branchDeliverySetting?->delivery_schedule_end;
        $this->checkDeliveryTimeAvailability();
    }

    // New method to check delivery time availability
    private function checkDeliveryTimeAvailability(): bool
    {
        // If no delivery schedule is set, delivery is available 24/7
        if (!$this->deliveryScheduleStart || !$this->deliveryScheduleEnd) {
            $this->isDeliveryTimeAvailable = true;
            return true;
        }

        // Get current time in restaurant's timezone
        $currentTime = Carbon::now()->timezone($this->restaurantTimezone);
        $currentTimeFormatted = $currentTime->format('H:i:s');

        // Parse delivery schedule times
        $startTime = Carbon::createFromFormat('H:i:s', $this->deliveryScheduleStart, $this->restaurantTimezone);
        $endTime = Carbon::createFromFormat('H:i:s', $this->deliveryScheduleEnd, $this->restaurantTimezone);

        // If end time is before start time, it means the delivery window spans across midnight
        if ($endTime->lt($startTime)) {
            $this->isDeliveryTimeAvailable = $currentTime->gte($startTime) || $currentTime->lt($endTime);
        } else {
            $this->isDeliveryTimeAvailable = $currentTime->gte($startTime) && $currentTime->lt($endTime);
        }

        // Calculate next available time if currently not available
        if (!$this->isDeliveryTimeAvailable) {
            if ($currentTime->lt($startTime)) {
                $this->nextAvailableTime = $startTime->format('H:i');
            } else {
                $this->nextAvailableTime = $startTime->addDay()->format('H:i');
            }
        }

        return $this->isDeliveryTimeAvailable;
    }

    public function toggleManualLocation()
    {
        if (!$this->checkDeliveryTimeAvailability()) {
            $this->alert('error', __('modules.delivery.outsideDeliveryHours', [
                'start' => Carbon::parse($this->deliveryScheduleStart)->format('g:i A'),
                'end' => Carbon::parse($this->deliveryScheduleEnd)->format('g:i A'),
                'timezone' => $this->restaurantTimezone
            ]));
            return;
        }

        $this->showManualLocation = !$this->showManualLocation;
        $this->selectedAddressId = null;

        if ($this->showManualLocation) {
            $this->dispatch('initDeliveryMap', [
                'branchLat' => $this->branchLat,
                'branchLng' => $this->branchLng,
                'maxKm' => $this->maxKm,
                'defaultLat' => $this->customerLat,
                'defaultLng' => $this->customerLng
            ]);
        }
    }

    public function checkDeliveryRange(): void
    {
        if (!$this->validateCoordinates()) {
            return;
        }

        // First check if delivery is available during current time
        $this->checkDeliveryTimeAvailability();

        if (!$this->isDeliveryTimeAvailable) {
            $this->inRange = false;
            $this->deliveryMessage = __('modules.delivery.outsideDeliveryHours', [
                'start' => Carbon::parse($this->deliveryScheduleStart)->format('g:i A'),
                'end' => Carbon::parse($this->deliveryScheduleEnd)->format('g:i A'),
                'timezone' => $this->restaurantTimezone
            ]);
            return;
        }

        $distance = $this->haversine($this->branchLat, $this->branchLng, $this->customerLat, $this->customerLng);
        $this->updateDeliveryRange($distance);
    }

    private function validateCoordinates(): bool
    {
        if (!$this->customerLat || !$this->customerLng || !$this->branchLat || !$this->branchLng) {
            $this->alert('error', __('messages.invalidCoordinates'), [
                'toast' => true,
                'position' => 'top-end',
            ]);
            return false;
        }
        return true;
    }

    private function updateDeliveryRange(float $distance): void
    {
        $feeDetails = $this->calculateDeliveryFee($this->branch, $this->customerLat, $this->customerLng, $this->orderGrandTotal);

        $this->deliveryFee = $feeDetails['fee'] ?? null;
        $this->deliveryMessage = $feeDetails['message'] ?? null;
        $this->inRange = $feeDetails['available'];
        $this->isFreeDelivery = $feeDetails['isFreeDelivery'] ?? false;
        $this->distance = $feeDetails['distance'] ?? null;
        $this->etaMin = $feeDetails['eta_min'] ?? null;
        $this->etaMax = $feeDetails['eta_max'] ?? null;

        if (!$feeDetails['available']) {
            $this->alert('error', $this->deliveryMessage ?? __('modules.delivery.locationOutOfRange'), [
                'toast' => true,
                'position' => 'top-end',
            ]);
        }
    }

    private function calculateDeliveryFee(Branch $branch, float $lat, float $lng, float $orderGrandTotal = 0): array
    {
        $deliverySettings = $branch->deliverySetting;

        if (!$deliverySettings->is_enabled) {
            return $this->buildResponse(false, __('modules.delivery.deliveryNotAvailable'));
        }

        // Check if current time is within delivery hours
        if (!$this->isDeliveryTimeAvailable) {
            return $this->buildResponse(false, $this->deliveryMessage);
        }

        $distance = $this->haversine($branch->lat, $branch->lng, $lat, $lng);

        $maxRadius = $this->convertToKm($deliverySettings->max_radius, $deliverySettings->unit);

        $freeDeliveryRadius = $deliverySettings->free_delivery_within_radius
            ? $this->convertToKm($deliverySettings->free_delivery_within_radius, $deliverySettings->unit)
            : null;

        $avgSpeedKmh = $this->convertToKm($deliverySettings->avg_delivery_speed_kmh, $deliverySettings->unit);

        if ($distance > $maxRadius) {
            return $this->buildResponse(false, __('modules.delivery.locationOutOfRange'));
        }

        // Use the actual orderGrandTotal from the component property
        if ($this->freeDeliveryOverAmount !== null && $orderGrandTotal >= $this->freeDeliveryOverAmount) {
            return $this->buildResponse(true, null, 0, $distance, $avgSpeedKmh, true);
        }

        if ($freeDeliveryRadius && $distance <= $freeDeliveryRadius) {
            return $this->buildResponse(true, null, 0, $distance, $avgSpeedKmh, true);
        }

        // Calculate fee based on type
        $fee = $this->calculateFee($deliverySettings, $branch, $distance);

        return $this->buildResponse(true, null, $fee, $distance, $avgSpeedKmh);
    }

    private function convertToKm(float $value, string $unit): float
    {
        return $unit === 'miles' ? $value * 1.60934 : $value;
    }

    private function calculateFee($deliverySettings, Branch $branch, float $distance): float
    {
        return match ($deliverySettings->fee_type) {
            DeliveryFeeType::FIXED => $deliverySettings->fixed_fee,
            DeliveryFeeType::PER_DISTANCE => $this->calculatePerKmFee($distance, $deliverySettings->per_distance_rate, $deliverySettings->unit),
            DeliveryFeeType::TIERED => $this->getTieredFee($branch, $distance),
        };
    }

    private function calculatePerKmFee(float $distance, float $rate, string $unit): float
    {
        // Convert distance to the correct unit (km or miles) based on the delivery settings
        if ($unit === 'miles') {
            // Convert km to miles before applying the rate
            $distance = $distance / 1.60934;
        }

        // Round up to the next whole unit (km or mile) and multiply by rate
        $wholeUnits = ceil($distance);
        return $wholeUnits * $rate;
    }

    private function buildResponse(bool $available, ?string $message, float $fee = 0, float $distance = 0, float $avgSpeedKmh = 0, ?bool $isFreeDelivery = false): array
    {
        if (!$available) {
            return ['available' => $available, 'message' => $message];
        }

        [$etaMin, $etaMax] = $this->calculateETA($distance, $avgSpeedKmh, $this->maxPreparationTime);
        return [
            'available' => $available,
            'fee' => $fee,
            'distance' => $distance,
            'eta_min' => $etaMin,
            'eta_max' => $etaMax,
            'isFreeDelivery' => $isFreeDelivery,
        ];
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earth = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earth * $c;
    }

    private function calculateETA(float $distance, float $speed, ?int $prepTime = 0): array
    {
        $additionalBufferTime = $this->branch->deliverySetting->additional_eta_buffer_time ?? 0;

        // Attempt to fetch travel time using Google Distance Matrix API
        $travelTimeMinutes = $this->getTravelTimeWithTraffic($this->branchLat, $this->branchLng, $this->customerLat, $this->customerLng);

        if ($travelTimeMinutes === null) {
            // Fallback to manual calculation if API fails
            $travelTimeMinutes = $this->calculateManualTravelTime($distance, $speed);
        }

        $totalMinMin = $prepTime + $travelTimeMinutes + $additionalBufferTime;
        $totalMaxMin = $prepTime + ceil($travelTimeMinutes * 1.3) + $additionalBufferTime;

        return [$totalMinMin, $totalMaxMin];
    }

    private function calculateManualTravelTime(float $distance, float $speed): int
    {
        if ($speed <= 0) {
            \Log::warning('Invalid speed provided for manual ETA calculation.');
            return 0; // Default to 0 if speed is invalid
        }

        $travelTimeHours = $distance / $speed;
        return ceil($travelTimeHours * 60); // Convert hours to minutes
    }

    private function getTravelTimeWithTraffic(float $originLat, float $originLng, float $destLat, float $destLng): ?int
    {
        info('Fetching travel time with traffic from Google Distance Matrix API');
        $apiKey = $this->mapApiKey;
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$originLat},{$originLng}&destinations={$destLat},{$destLng}&departure_time=now&key={$apiKey}";

        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if ($data['status'] === 'OK' && $data['rows'][0]['elements'][0]['status'] === 'OK') {
                $durationInSeconds = $data['rows'][0]['elements'][0]['duration_in_traffic']['value'] ?? $data['rows'][0]['elements'][0]['duration']['value'];
                return ceil($durationInSeconds / 60);
            }
        } catch (\Exception $e) {
            \Log::error('Google Distance Matrix API Error: ' . $e->getMessage());
        }

        return null;
    }

    private function getTieredFee(Branch $branch, float $distance): float
    {
        $deliverySettings = $branch->deliverySetting;

        if ($deliverySettings->unit === 'miles') {
            $distance /= 1.60934; // Convert kilometers to miles
        }

        // First try to find an exact tier match (distance falls within min/max range)
        $deliveryTiers = $branch->deliveryFeeTiers;
        // Find exact tier match
        $exactTier = $deliveryTiers
            ->where('min_distance', '<=', $distance)
            ->where('max_distance', '>=', $distance)
            ->first();

        if ($exactTier) {
            return $exactTier->fee;
        }

        // Find highest tier below distance
        $applicableTier = $deliveryTiers
            ->where('max_distance', '<', $distance)
            ->sortByDesc('max_distance')
            ->first();

        if ($applicableTier) {
            return $applicableTier->fee;
        }

        // Find lowest tier if distance is below minimum
        $lowestTier = $deliveryTiers
            ->sortBy('min_distance')
            ->first();

        if ($lowestTier && $distance < $lowestTier->min_distance) {
            return $lowestTier->fee;
        }

        // If we get here, we couldn't find any applicable tier
        return 0;
    }

    public function handleLocationSelected($lat, $lng, $address)
    {
        $this->customerLat = $lat;
        $this->customerLng = $lng;
        $this->selectedAddress = $address;
        $this->checkDeliveryRange();
    }

    public function selectAddressFromSaved($addressId)
    {

        $address = $this->customerAllAddresses->where('id', $addressId)->first();

        if (!$address) {
            $this->alert('error', __('messages.addressNotFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
            ]);
            return;
        }

        // Update coordinates and check delivery range
        $this->customerLat = $address->lat;
        $this->customerLng = $address->lng;
        $this->checkDeliveryRange();

        // Mark this address as selected but don't confirm it yet
        $this->selectedAddressId = $address->id;
        $this->selectedAddress = $address->address;
    }

    public function confirmSelectedAddress()
    {

        if (!$this->checkDeliveryTimeAvailability()) {
            $this->alert('error', __('modules.delivery.outsideDeliveryHours', [
                'start' => Carbon::parse($this->deliveryScheduleStart)->format('g:i A'),
                'end' => Carbon::parse($this->deliveryScheduleEnd)->format('g:i A'),
                'timezone' => $this->restaurantTimezone
            ]));
            return;
        }

        if (!$this->selectedAddressId) {
            $this->alert('error', __('messages.noAddressSelected'), [
                'toast' => true,
                'position' => 'top-end',
            ]);
            return;
        }

        $address = $this->customerAllAddresses->where('id', $this->selectedAddressId)->first();

        if (!$address) {
            $this->alert('error', __('messages.addressNotFound'), [
                'toast' => true,
                'position' => 'top-end',
            ]);
            return;
        }

        if (!$this->inRange) {
            $this->alert('error', $this->deliveryMessage ?? __('messages.outOfDeliveryRange'), [
                'toast' => true,
                'position' => 'top-end',
            ]);
            return;
        }

        // Now actually confirm and dispatch the selection
        $this->selectedLat = $address->lat;
        $this->selectedLng = $address->lng;
        $this->selectedAddress = $address->address;

        $this->dispatch('selectedDeliveryDetails', [
            'lat' => $address->lat,
            'lng' => $address->lng,
            'address' => $address->address,
            'deliveryFee' => $this->deliveryFee,
            'eta_min' => $this->etaMax,
            'eta_max' => $this->etaMax,
        ]);
    }

    public function selectDeliveryAddress()
    {
        $this->validate([
            'customerLat' => 'required|numeric',
            'customerLng' => 'required|numeric',
            'selectedAddress' => 'required|string|min:5'
        ]);

        if (!$this->checkDeliveryTimeAvailability()) {
            $this->alert('error', __('modules.delivery.outsideDeliveryHours', [
                'start' => Carbon::parse($this->deliveryScheduleStart)->format('g:i A'),
                'end' => Carbon::parse($this->deliveryScheduleEnd)->format('g:i A'),
                'timezone' => $this->restaurantTimezone
            ]));
            return;
        }

        if (!$this->inRange) {
            $this->alert('error', $this->deliveryMessage ?? __('messages.outOfDeliveryRange'));
            return;
        }


        if (!$this->customerLat || !$this->customerLng) {
            $this->alert('error', __('messages.invalidCoordinates'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
            ]);
            return;
        }

        $this->selectedLat = $this->customerLat;
        $this->selectedLng = $this->customerLng;

        $this->dispatch('selectedDeliveryDetails', [
            'lat' => $this->selectedLat,
            'lng' => $this->selectedLng,
            'address' => $this->selectedAddress,
            'deliveryFee' => $this->deliveryFee,
            'eta_min' => $this->etaMin,
            'eta_max' => $this->etaMax,
        ]);
    }

    public function render()
    {
        return view('livewire.customer.location-selector');
    }
}
