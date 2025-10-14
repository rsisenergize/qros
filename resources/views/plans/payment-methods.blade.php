
<div class="payment_methods">
    {{-- Online Payment Options --}}
    @if($showOnline && $paymentGatewayActive)
        <div class="grid items-baseline grid-cols-1 gap-4 space-y-4 md:grid-cols-3">
            @if($stripeSettings->stripe_status === 1)
                <button wire:click="initiateStripePayment({{ $selectedPlan?->id }})" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="24" height="24" fill="#6772e5"><path d="M111.328 15.602c0-4.97-2.415-8.9-7.013-8.9s-7.423 3.924-7.423 8.863c0 5.85 3.32 8.8 8.036 8.8 2.318 0 4.06-.528 5.377-1.26V19.22a10.25 10.25 0 0 1-4.764 1.075c-1.9 0-3.556-.67-3.774-2.943h9.497a40 40 0 0 0 .063-1.748zm-9.606-1.835c0-2.186 1.35-3.1 2.56-3.1s2.454.906 2.454 3.1zM89.4 6.712a5.43 5.43 0 0 0-3.801 1.509l-.254-1.208h-4.27v22.64l4.85-1.032v-5.488a5.43 5.43 0 0 0 3.444 1.265c3.472 0 6.64-2.792 6.64-8.957.003-5.66-3.206-8.73-6.614-8.73zM88.23 20.1a2.9 2.9 0 0 1-2.288-.906l-.03-7.2a2.93 2.93 0 0 1 2.315-.96c1.775 0 2.998 2 2.998 4.528.003 2.593-1.198 4.546-2.995 4.546zM79.25.57l-4.87 1.035v3.95l4.87-1.032z" fill-rule="evenodd"/><path d="M74.38 7.035h4.87V24.04h-4.87z"/><path d="m69.164 8.47-.302-1.434h-4.196V24.04h4.848V12.5c1.147-1.5 3.082-1.208 3.698-1.017V7.038c-.646-.232-2.913-.658-4.048 1.43zm-9.73-5.646L54.698 3.83l-.02 15.562c0 2.87 2.158 4.993 5.038 4.993 1.585 0 2.756-.302 3.405-.643v-3.95c-.622.248-3.683 1.138-3.683-1.72v-6.9h3.683V7.035h-3.683zM46.3 11.97c0-.758.63-1.05 1.648-1.05a10.9 10.9 0 0 1 4.83 1.25V7.6a12.8 12.8 0 0 0-4.83-.888c-3.924 0-6.557 2.056-6.557 5.488 0 5.37 7.375 4.498 7.375 6.813 0 .906-.78 1.186-1.863 1.186-1.606 0-3.68-.664-5.307-1.55v4.63a13.5 13.5 0 0 0 5.307 1.117c4.033 0 6.813-1.992 6.813-5.485 0-5.796-7.417-4.76-7.417-6.943zM13.88 9.515c0-1.37 1.14-1.9 2.982-1.9A19.66 19.66 0 0 1 25.6 9.876v-8.27A23.2 23.2 0 0 0 16.862.001C9.762.001 5 3.72 5 9.93c0 9.716 13.342 8.138 13.342 12.326 0 1.638-1.4 2.146-3.37 2.146-2.905 0-6.657-1.202-9.6-2.802v8.378A24.4 24.4 0 0 0 14.973 32C22.27 32 27.3 28.395 27.3 22.077c0-10.486-13.42-8.613-13.42-12.56z" fill-rule="evenodd"/></svg>
                    @lang('modules.billing.stripe')
                </button>
            @endif
            @if($stripeSettings->razorpay_status === 1)
                <button wire:click="razorpaySubscription({{ $selectedPlan?->id }});" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn">
                    <svg class="w-4 h-4" width="24" height="24" viewBox="0 0 24 24"><defs><linearGradient id="a" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#0d3e8e"/><stop offset="100%" stop-color="#00c3f3"/></linearGradient></defs><path fill="url(#a)" d="m22.436 0-11.91 7.773-1.174 4.276 6.625-4.297L11.65 24h4.391z"/><path fill="#0D3E8E" d="M14.26 10.098 3.389 17.166 1.564 24h9.008z"/></svg>
                    @lang('modules.billing.razorpay')
                </button>
            @endif
            @if($stripeSettings->flutterwave_status === 1)
                <button wire:click="initiateFlutterwavePayment({{ $selectedPlan?->id }});" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn">
                    <svg class="w-4 h-4 mr-2" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 176 144.7" xml:space="preserve"><path d="M0 31.6c0-9.4 2.7-17.4 8.5-23.1l10 10C7.4 29.6 17.1 64.1 48.8 95.8s66.2 41.4 77.3 30.3l10 10c-18.8 18.8-61.5 5.4-97.3-30.3C14 80.9 0 52.8 0 31.6" style="fill:#009a46"/><path d="M63.1 144.7c-9.4 0-17.4-2.7-23.1-8.5l10-10c11.1 11.1 45.6 1.4 77.3-30.3s41.4-66.2 30.3-77.3l10-10c18.8 18.8 5.4 61.5-30.3 97.3-24.9 24.8-53.1 38.8-74.2 38.8" style="fill:#ff5805"/><path d="M140.5 91.6C134.4 74.1 122 55.4 105.6 39 69.8 3.2 27.1-10.1 8.3 8.6 7 10 8.2 13.3 10.9 16s6.1 3.9 7.4 2.6c11.1-11.1 45.6-1.4 77.3 30.3 15 15 26.2 31.8 31.6 47.3 4.7 13.6 4.3 24.6-1.2 30.1-1.3 1.3-.2 4.6 2.6 7.4s6.1 3.9 7.4 2.6c9.6-9.7 11.2-25.6 4.5-44.7" style="fill:#f5afcb"/><path d="M167.5 8.6C157.9-1 142-2.6 122.9 4c-17.5 6.1-36.2 18.5-52.6 34.9-35.8 35.8-49.1 78.5-30.3 97.3 1.3 1.3 4.7.2 7.4-2.6s3.9-6.1 2.6-7.4c-11.1-11.1-1.4-45.6 30.3-77.3 15-15 31.8-26.2 47.2-31.6 13.6-4.7 24.6-4.3 30.1 1.2 1.3 1.3 4.6.2 7.4-2.6s3.9-5.9 2.5-7.3" style="fill:#ff9b00"/></svg>
                    @lang('modules.billing.flutterwave')
                </button>
            @endif
            @if($stripeSettings->paypal_status === 1)
                <button wire:click="initiatePaypalPayment({{ $selectedPlan?->id }});" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" aria-label="PayPal" role="img" viewBox="0 0 512 512" width="64px" height="64px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect width="512" height="512" rx="15%" fill="#ffffff"></rect><path fill="#002c8a" d="M377 184.8L180.7 399h-72c-5 0-9-5-8-10l48-304c1-7 7-12 14-12h122c84 3 107 46 92 112z"></path><path fill="#009be1" d="M380.2 165c30 16 37 46 27 86-13 59-52 84-109 85l-16 1c-6 0-10 4-11 10l-13 79c-1 7-7 12-14 12h-60c-5 0-9-5-8-10l22-143c1-5 182-120 182-120z"></path><path fill="#001f6b" d="M197 292l20-127a14 14 0 0 1 13-11h96c23 0 40 4 54 11-5 44-26 115-128 117h-44c-5 0-10 4-11 10z"></path></g></svg>
                    @lang('modules.billing.paypal')
                </button>
            @endif
            @if($stripeSettings->payfast_status === 1)
                <button wire:click="initiatePayfastPayment({{ $selectedPlan?->id }});" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn">
                    <svg class="w-5 h-4" width="24" height="24" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill="none"><g fill="#E63946"><ellipse cx="32" cy="12" rx="20" ry="8"/><path d="M12 12v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8Z"/><path d="M12 24v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8Z"/><path d="M12 36v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8Z"/></g></svg>
                    @lang('modules.billing.payfast')
                </button>
            @endif
            @if($stripeSettings->paystack_status === 1)
                <button wire:click="initiatePaystackPayment({{ $selectedPlan?->id }});" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#0AA5FF"><path d="M2 3.6c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V3.6Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V8.4Zm0 4.8c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Z" fill-rule="evenodd"/></svg>
                    @lang('modules.billing.paystack')
                </button>
            @endif
            @if($stripeSettings->xendit_status === 1)
                <button wire:click="initiateXenditPayment({{ $selectedPlan?->id }});" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn">
                          <svg class="w-4 h-4 mr-2" role="img" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" id="Xendit--Streamline-Simple-Icons" height="24" width="24">
                            <desc>
                              Xendit Streamline Icon: https://streamlinehq.com
                            </desc>
                            <title>Xendit</title>
                            <path d="M11.781 2.743H7.965l-5.341 9.264 5.341 9.263 -1.312 2.266L0 12.007 6.653 0.464h6.454l-1.326 2.279Zm-5.128 2.28 1.312 -2.28L9.873 6.03 8.561 8.296 6.653 5.023Zm9.382 -2.28 1.312 2.28L7.965 21.27l-1.312 -2.279 9.382 -16.248Zm-5.128 20.793 1.298 -2.279h3.83L14.1 17.931l1.312 -2.267 1.926 3.337 4.038 -6.994 -5.341 -9.264L17.347 0.464 24 12.007l-6.653 11.529h-6.44Z" fill="#000000" stroke-width="1"></path>
                          </svg>
                          @lang('modules.billing.xendit')
                </button>
            @endif
            @if($stripeSettings->paddle_status === 1)
                <button
                    wire:click="initiatePaddlePayment({{ $selectedPlan?->id }});"
                    class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 64 64" role="img" aria-label="Paddle-like icon" class="mr-2">
                        <rect width="64" height="64" rx="10" ry="10" fill="#0B0B0B"/>
                        <rect x="4" y="4" width="56" height="56" rx="8" ry="8" fill="none" opacity="0.06"/>
                        <path d="M32 16 L35.5 25.5 L45.5 26 L37.5 31.5 L40 41 L32 35 L24 41 L26.5 31.5 L18.5 26 L28.5 25.5 Z" fill="#FFD34D"/>
                    </svg>
                    Paddle
                </button>
            @endif

            @if($offlinePaymentGateways > 0)
                <button wire:click="togglePaymentOptions('0')" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn gap-x-1">
                    <svg class="w-4 h-4 text-gray-800 dark:text-gray-300" width="24" height="24" viewBox="0 0 64 64" fill="currentColor" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2"><path style="fill:none" d="M-576 0H704v800H-576z"/><g/><g/><g/><g/><g/><g/><g/><g/><path d="m14.563 17.167 7.468-7.468 2.61 2.61-10.107 10.107-6.359-6.358 2.64-2.64zm41.456.806H28.026v-3.969h27.993z"/><path d="m14.563 17.167 7.468-7.468 2.61 2.61-10.107 10.107-6.359-6.358 2.64-2.64zm41.456.806H28.026v-3.969h27.993zM14.563 49.167l7.468-7.468 2.61 2.61-10.107 10.107-6.359-6.358 2.64-2.64zm41.456.806H28.026v-3.969h27.993z"/><path d="m14.563 49.167 7.468-7.468 2.61 2.61-10.107 10.107-6.359-6.358 2.64-2.64zm41.456.806H28.026v-3.969h27.993zM14.563 33.167l7.468-7.468 2.61 2.61-10.107 10.107-6.359-6.358 2.64-2.64zm41.456.806H28.026v-3.969h27.993z"/><path d="m14.563 33.167 7.468-7.468 2.61 2.61-10.107 10.107-6.359-6.358 2.64-2.64zm41.456.806H28.026v-3.969h27.993z"/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></svg>
                    @lang('modules.billing.payOffline')
                </button>
            @endif
        </div>
    @endif

    {{-- Offline Payment Options --}}
    @if(!$showOnline && $offlinePaymentGateways > 0)
        @if($paymentGatewayActive)
            <button wire:click="togglePaymentOptions('1')" class="flex items-center w-full px-4 py-3 border rounded btn-light hover:shadow hover:bg-indigo-50 dark:hover:bg-gray-500 f-15 btn gap-x-1">
                <svg class="w-4 h-4 text-gray-800 dark:text-gray-300" width="24" height="24" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="currentColor" fill="none"><rect x="8.33" y="10.82" width="47.34" height="34.38" rx="2.02"/><path d="M8.33 38.66h47.34M23.05 53.18a6.67 6.67 0 0 0 3.55-8m15.24 8a6.66 6.66 0 0 1-3.55-8"/><path stroke-linecap="round" d="M17.5 53.18h29"/><path d="m16.8 19.18 2.9 2.03 3.59-5.05m-6.91 12.82L19.27 31l3.59-5.05m5.74-6.47h20.82M28.6 29.66h20.82"/></svg>
                @lang('modules.billing.payOnline')
            </button>
        @endif
        <div class="grid grid-cols-1 gap-6 mt-6">
            @foreach($methods as $method)
                <label class="relative flex flex-col gap-4 px-4 py-3 text-gray-700 transition bg-white border border-gray-200 rounded-md shadow dark:bg-gray-700 hover:border-bg-indigo-300 hover:bg-indigo-50 dark:hover:bg-gray-600 focus-within:ring-2 focus-within:ring-indigo-500 peer-checked:border-indigo-500 peer-checked:bg-indigo-100 dark:peer-checked:text-indigo-900">
                    <span class="text-lg font-medium dark:text-gray-300">{{ $method->name }}</span>
                    <input type="radio" name="PaymentOption" wire:model.live="offlineMethodId" value="{{ $method->id }}" class="absolute inset-0 z-10 opacity-0 cursor-pointer peer"/>
                    <span class="absolute top-1.5 right-1.5 hidden w-5 h-5 rounded-full bg-skin-base text-white flex items-center peer-checked:flex p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <div class="text-xs text-gray-400">
                        {!! nl2br(e($method->description)) !!}
                    </div>
                </label>
            @endforeach
        </div>
    @endif
</div>
