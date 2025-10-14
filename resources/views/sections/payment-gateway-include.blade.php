@if (superadminPaymentGateway()->stripe_status && !is_null(superadminPaymentGateway()->stripe_key))
<script src="https://js.stripe.com/v3/"></script>

<form action="{{ route('stripe.license_payment') }}" method="POST" id="license-payment-form" class="hidden">
    @csrf

    <input type="hidden" id="license_payment" name="license_payment">
    <input type="hidden" id="package_type" name="package_type">
    <input type="hidden" id="package_id" name="package_id">
    <input type="hidden" id="currency_id" name="currency_id">

    <div class="form-row">
        <label for="card-element">
            Credit or debit card
        </label>
        <div id="card-element">
            <!-- A Stripe Element will be inserted here. -->
        </div>

        <!-- Used to display Element errors. -->
        <div id="card-errors" role="alert"></div>
    </div>

    <button>Submit Payment</button>
</form>


<script>
    const stripe = Stripe('{{ superadminPaymentGateway()->stripe_key }}');
    const elements = stripe.elements({
        currency: '{{ strtolower(restaurant()->currency->currency_code) }}',
    });
</script>
@endif

@if (superadminPaymentGateway()->flutterwave_status)
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <form action="{{ route('flutterwave.initiate-payment') }}" method="POST" id="flutterwavePaymentformNew" class="hidden">
        @csrf
        <input type="hidden" name="payment_id">
        <input type="hidden" name="amount">
        <input type="hidden" name="currency">
        <input type="hidden" name="restaurant_id">
        <input type="hidden" name="package_id">
        <input type="hidden" name="package_type">
    </form>
@endif

@if (superadminPaymentGateway()->paypal_status)
    <script src="https://www.paypal.com/sdk/js?client-id={{ superadminPaymentGateway()->paypal_client_id }}&currency={{ restaurant()->currency->currency_code }}"></script>
    <form action="{{ route('paypal.initiate-payment') }}" method="POST" id="paypalPaymentForm" class="hidden">
        @csrf
        <input type="hidden" name="payment_id">
        <input type="hidden" name="amount">
        <input type="hidden" name="currency">
        <input type="hidden" name="restaurant_id">
        <input type="hidden" name="package_id">
        <input type="hidden" name="package_type">
    </form>
@endif

@if (superadminPaymentGateway()->payfast_status)
    <form action="{{ route('payfast.initiate-payment') }}" method="POST" id="payfastPaymentForm" class="hidden">
        @csrf
        <input type="hidden" name="payment_id">
        <input type="hidden" name="amount">
        <input type="hidden" name="currency">
        <input type="hidden" name="restaurant_id">
        <input type="hidden" name="package_id">
        <input type="hidden" name="package_type">
    </form>
@endif

@if (superadminPaymentGateway()->paystack_status)
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <form action="{{ route('paystack.initiate-payment') }}" method="POST" id="paystackPaymentformNew" class="hidden">
        @csrf
        <input type="hidden" name="payment_id">
        <input type="hidden" name="amount">
        <input type="hidden" name="currency">
        <input type="hidden" name="restaurant_id">
        <input type="hidden" name="package_id">
        <input type="hidden" name="package_type">
        <input type="hidden" name="email">
    </form>
@endif

@if (superadminPaymentGateway()->xendit_status)
    <script src="https://js.xendit.co/v1/xendit.min.js"></script>
    <form action="{{ route('xendit.subscription.initiate') }}" method="POST" id="xenditPaymentformNew" class="hidden">
        @csrf
        <input type="hidden" name="payment_id">
        <input type="hidden" name="amount">
        <input type="hidden" name="currency">
        <input type="hidden" name="restaurant_id">
        <input type="hidden" name="package_id">
        <input type="hidden" name="package_type">
        <input type="hidden" name="email">
    </form>
@endif

@if (superadminPaymentGateway()->paddle_status)
    <form action="{{ route('paddle.subscription.initiate') }}" method="POST" id="paddlePaymentformNew" class="hidden">
        @csrf
        <input type="hidden" name="payment_id">
        <input type="hidden" name="amount">
        <input type="hidden" name="currency">
        <input type="hidden" name="restaurant_id">
        <input type="hidden" name="package_id">
        <input type="hidden" name="package_type">
        <input type="hidden" name="email">
    </form>
@endif
