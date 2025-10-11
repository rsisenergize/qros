<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\ReservationSetting;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Events\TodayReservationCreatedEvent;

class NewReservation extends Component
{

    use LivewireAlert;

    public $reservationSettings;
    public $date;
    public $period;
    public $numberOfGuests;
    public $slotType;
    public $specialRequest;
    public $availableTimeSlots = [];
    public $customerName;
    public $customerPhone;
    public $customerEmail;

    // Time slot options (empty until date and slot type are selected)
    public $timeSlots = [];

    public function mount()
    {
        $this->date = now(timezone())->format('Y-m-d');
        $this->slotType = 'Lunch';
        $this->numberOfGuests = 1;
        $this->loadAvailableTimeSlots();
    }

    public function updatedDate()
    {
        $this->loadAvailableTimeSlots();
    }

    public function setReservationGuest($noOfGuests)
    {
        // Get minimum party size from restaurant settings
        $minimumPartySize = restaurant() ? (restaurant()->minimum_party_size ?? 1) : 1;

        // Validate that the selected number of guests meets the minimum requirement
        if ($noOfGuests < $minimumPartySize) {
            $this->addError('numberOfGuests', __('messages.minimumPartySizeRequired', ['size' => $minimumPartySize]));
            return;
        }

        $this->numberOfGuests = $noOfGuests;
    }

    public function setReservationSlotType($type)
    {
        $this->slotType = $type;
        $this->loadAvailableTimeSlots();
    }

    public function updatedSlotType()
    {
        $this->loadAvailableTimeSlots();
    }

    public function loadAvailableTimeSlots()
    {
        $this->timeSlots = [];

        if ($this->date && $this->slotType) {
            // Ensure we're using the correct date format
            $parsedDate = Carbon::parse($this->date);
            $dayOfWeek = $parsedDate->format('l');
            $selectedDate = $parsedDate->format('Y-m-d');
            $currentTimezone = timezone();

            $now = Carbon::now($currentTimezone);
            $restaurant = restaurant();
            $disableSlotMinutes = $restaurant ? (int)($restaurant->disable_slot_minutes ?? 30) : 30;
            $currentTimeWithBuffer = $now->copy()->addMinutes($disableSlotMinutes);

            $settings = ReservationSetting::where('day_of_week', $dayOfWeek)
                ->where('slot_type', $this->slotType)
                ->where('available', 1)
                ->first();

            if ($settings) {
                // Generate time slots based on the time slot difference
                $startTime = Carbon::parse($settings->time_slot_start);
                $endTime = Carbon::parse($settings->time_slot_end);
                $slotDifference = (int)$settings->time_slot_difference;

                while ($startTime->lte($endTime)) {
                    $slotTime = $startTime->format('H:i:s');
                    $slotDateTime = Carbon::parse("{$selectedDate} {$slotTime}", $currentTimezone);

                    // Check if this is today and if the slot should be disabled
                    $isToday = $selectedDate === $now->format('Y-m-d');
                    $isDisabled = false;

                    if ($isToday) {
                        // For today, check if the slot is within the buffer time
                        $isDisabled = $slotDateTime->lte($currentTimeWithBuffer);
                    }

                    $this->timeSlots[] = [
                        'time' => $slotTime,
                        'disabled' => $isDisabled
                    ];

                    $startTime->addMinutes($slotDifference);
                }
            }
        }
    }

    public function submitReservation()
    {
        // Get minimum party size from restaurant settings
        $minimumPartySize = restaurant()->minimum_party_size ?? 1;

        $this->validate([
            'availableTimeSlots' => 'required',
            'customerName' => 'required',
            'numberOfGuests' => "required|integer|min:{$minimumPartySize}",
        ]);

        if ($this->availableTimeSlots) {
            $selectedDate = Carbon::parse($this->date)->format('Y-m-d');
            $currentTimezone = timezone() ?: 'Asia/Kolkata';
            $now = Carbon::now($currentTimezone);
            $restaurant = restaurant();
            $disableSlotMinutes = $restaurant ? (int)($restaurant->disable_slot_minutes ?? 30) : 30;
            $currentTimeWithBuffer = $now->copy()->addMinutes($disableSlotMinutes);

            $slotDateTime = Carbon::parse("{$selectedDate} {$this->availableTimeSlots}", $currentTimezone);
            $isToday = $selectedDate === $now->format('Y-m-d');

            if ($isToday && $slotDateTime->lte($currentTimeWithBuffer)) {
                $this->addError('availableTimeSlots', __('messages.slotDisabled'));
                return;
            }
        }

        $existingCustomer = Customer::where('email', $this->customerEmail)->first();

        if ($existingCustomer) {
            $existingCustomer->update([
                'name' => $this->customerName,
                'phone' => $this->customerPhone
            ]);
            $customer = $existingCustomer;
        } else {
            $customer = Customer::create([
                'name' => $this->customerName,
                'phone' => $this->customerPhone,
                'email' => $this->customerEmail
            ]);
        }

        Reservation::create([
            'reservation_date_time' => $this->date . ' ' . $this->availableTimeSlots,
            'customer_id' => $customer->id,
            'party_size' => $this->numberOfGuests,
            'reservation_slot_type' => $this->slotType,
            'special_requests' => $this->specialRequest,
            'slot_time_difference' => ReservationSetting::where('slot_type', $this->slotType)->first()->time_slot_difference
        ]);

        $this->alert('success', __('messages.reservationConfirmed'), [
            'toast' => false,
            'position' => 'center',
            'showCancelButton' => true,
            'cancelButtonText' => __('app.close')
        ]);

        return $this->redirect(route('reservations.index'));
    }

    public function render()
    {
        return view('livewire.forms.new-reservation');
    }
}
