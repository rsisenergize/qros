<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paddle Checkout - {{ $package->name ?? 'Package' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Complete Your Payment
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ $package->name ?? 'Package' }} - {{ ucfirst($subscription->package_type ?? 'subscription') }}
                </p>
                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ $package->currency->currency_symbol ?? '$' }}{{ number_format($amount, 2) }}
                    @if($subscription->package_type !== 'lifetime')
                        <span class="text-sm font-normal text-gray-500">/ {{ $subscription->package_type }}</span>
                    @endif
                </p>
            </div>

            <div class="mt-8 space-y-6">
                <div id="checkout-container" class="bg-white p-6 rounded-lg shadow">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-4 text-gray-600">Loading checkout...</p>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        ← Cancel and return to dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Initialize Paddle
        @if($isSandbox)
        Paddle.Environment.set("sandbox");
        @endif

        Paddle.Initialize({
            token: "{{ $clientToken }}",
        });

        // Open Paddle Checkout
        Paddle.Checkout.open({
            items: [{
                priceId: "{{ $priceId }}",
                quantity: 1
            }],
            customData: {
                restaurant_id: "{{ $subscription->restaurant_id }}",
                package_id: "{{ $subscription->package_id }}",
                package_type: "{{ $subscription->package_type }}",
                transaction_id: "{{ session('paddle_transaction_id') }}",
                subscription_id: "{{ $subscriptionId }}"
            },
            customer: {
                id: "{{ $customerId }}"
            },
            settings: {
                successUrl: "{{ route('paddle.subscription.callback') }}",
                locale: "en"
            }
        });

        // Handle checkout events
        Paddle.Checkout.on('checkout.completed', function(data) {
            console.log('Checkout completed:', data);
            // Redirect with transaction_id if available
            if (data.transaction_id) {
                window.location.href = "{{ route('paddle.subscription.callback') }}?paddle_transaction_id=" + data.transaction_id;
            } else {
                window.location.href = "{{ route('paddle.subscription.callback') }}";
            }
        });

        Paddle.Checkout.on('checkout.closed', function(data) {
            console.log('Checkout closed:', data);
            if (!data.completed) {
                window.location.href = "{{ route('paddle.subscription.failed') }}";
            }
        });

        Paddle.Checkout.on('checkout.error', function(error) {
            console.error('Checkout error:', error);
            alert('Payment error: ' + error.message);
            window.location.href = "{{ route('paddle.subscription.failed') }}";
        });
    </script>
</body>
</html>

