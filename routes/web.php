<?php



use App\Exports\PaymentExport;
use App\Http\Middleware\SuperAdmin;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KotController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\DisableFrontend;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TableController;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomMenuController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\LandingSiteController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CustomModuleController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemModifierController;
use App\Http\Controllers\GlobalSettingController;
use App\Http\Controllers\ModifierGroupController;
use App\Http\Controllers\WaiterRequestController;
use App\Http\Controllers\OnboardingStepController;
use App\Http\Controllers\PayfastPaymentController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\DeliveryExecutiveController;
use App\Http\Controllers\RestaurantPaymentController;
use App\Http\Controllers\RestaurantSettingController;
use App\Http\Controllers\SuperadminSettingController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\FlutterwavePaymentController;
use App\Http\Controllers\SuperAdmin\FlutterwaveController;
use App\Http\Controllers\SuperAdmin\StripeWebhookController;
use App\Http\Controllers\SuperAdmin\RazorpayWebhookController;
use App\Http\Controllers\SuperAdmin\FlutterwaveWebhookController;
use App\Http\Controllers\XenditPaymentController;
use App\Http\Controllers\SuperAdmin\PaypalController;
use App\Http\Controllers\PaypalPaymentController;
use App\Http\Controllers\SuperAdmin\PayFastWebhookController;
use App\Http\Controllers\SuperAdmin\PayfastController;
use App\Http\Controllers\SuperAdmin\PaystackWebhookController;
use App\Http\Controllers\SuperAdmin\PaystackController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PlanController;
use App\Livewire\CustomerDisplay;
use App\Http\Middleware\CustomerSiteMiddleware;
use App\Http\Middleware\VerifyRestaurantAccess;
use App\Http\Controllers\ViewPngController;


Route::get('/manifest.json', [HomeController::class, 'manifest'])->name('manifest');


Route::group(['prefix' => 'restaurant'], function () {
    Route::get('/table/{hash}', [ShopController::class, 'tableOrder'])->name('table_order')->where('id', '.*');
    Route::get('/my-orders/{hash}', [ShopController::class, 'myOrders'])->name('my_orders');
    Route::get('/my-bookings/{hash}', [ShopController::class, 'myBookings'])->name('my_bookings');
    Route::get('/my-addresses/{hash}',  [ShopController::class, 'myAddresses'])->name('my_addresses');
    Route::get('/book-a-table/{hash}', [ShopController::class, 'bookTable'])->name('book_a_table');
    Route::get('/contact/{hash}', [ShopController::class, 'contact'])->name('contact');
    Route::get('/about-us/{hash}', [ShopController::class, 'about'])->name('about');
    Route::get('/profile/{hash}', [ShopController::class, 'profile'])->name('profile');
    Route::get('/orders-success/{id}', [ShopController::class, 'orderSuccess'])->name('order_success');
});

Route::get('/restaurant/{hash}', [ShopController::class, 'cart'])->name('shop_restaurant');


// Only register the root route if Subdomain module is not enabled
if (!function_exists('module_enabled') || !module_enabled('Subdomain')) {
    Route::get('/', [HomeController::class, 'landing'])->name('home')->middleware(DisableFrontend::class);
    Route::get('/change-locale/{locale}', [HomeController::class, 'changeLocale'])->name('change.locale');
}

Route::get('/restaurant-signup', [HomeController::class, 'signup'])->name('restaurant_signup');
Route::get('/customer-logout', [HomeController::class, 'customerLogout'])->name('customer_logout');
Route::get('page/{slug}', [CustomMenuController::class, 'index'])->name('customMenu');



Route::post('stripe/order-payment', [StripeController::class, 'orderPayment'])->name('stripe.order_payment');
Route::get('/stripe/success-callback', [StripeController::class, 'success'])->name('stripe.success');

Route::post('stripe/license-payment', [StripeController::class, 'licensePayment'])->name('stripe.license_payment');
Route::get('/stripe/license-success-callback', [StripeController::class, 'licenseSuccess'])->name('stripe.license_success');
Route::post('/flutterwave/initiate-payment', [FlutterwaveController::class, 'initiatePayment'])->name('flutterwave.initiate-payment');
Route::get('/flutterwave/callback', [FlutterwaveController::class, 'paymentCallback'])->name('flutterwave.callback');

Route::post('/paypal/initiate-payment', [PaypalController::class, 'initiatePayment'])->name('paypal.initiate-payment');
Route::get('billing/paypal-recurring', [PaypalController::class, 'payWithPaypalRecurrring'])->name('billing.paypal-recurring');
Route::get('/paypal/lifetime/success', [PaypalController::class, 'paypalLifetimeSuccess'])->name('paypal.lifetime.success');

Route::post('/payfast/initiate-payment', [PayfastController::class, 'initiatePayfastPayment'])->name('payfast.initiate-payment');
Route::get('billing/payfast-success', [PayFastController::class, 'payFastPaymentSuccess'])->name('billing.payfast-success');
Route::get('billing/payfast-cancel', [PayFastController::class, 'payFastPaymentCancel'])->name('billing.payfast-cancel');

Route::post('/paystack/initiate-payment', [PaystackController::class, 'initiatePaystackPayment'])->name('paystack.initiate-payment');
Route::get('/paystack/callback', [PaystackController::class, 'handleGatewayCallback'])->name('paystack.callback');



Route::middleware(['auth', config('jetstream.auth_session'), 'verified', VerifyRestaurantAccess::class])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('account_unverified', [DashboardController::class, 'accountUnverified'])->name('account_unverified');

    Route::get('onboarding-steps', [OnboardingStepController::class, 'index'])->name('onboarding_steps');

    Route::resource('menus', MenuController::class);
    Route::get('menu-items/sort-entities', [MenuController::class, 'unifiedSort'])->name('menu-items.entities.sort');
    Route::get('menu-items/bulk-import', [MenuItemController::class, 'bulkImport'])->name('menu-items.bulk-import');
    Route::resource('menu-items', MenuItemController::class);
    Route::resource('item-categories', ItemCategoryController::class);
    Route::resource('item-modifiers', ItemModifierController::class);
    Route::resource('modifier-groups', ModifierGroupController::class);

    Route::resource('areas', AreaController::class);
    Route::resource('tables', TableController::class);

    Route::get('orders/print/{id}', [OrderController::class, 'printOrder'])->name('orders.print');
    Route::get('orders/pdf/{id}', [OrderController::class, 'generateOrderPdf'])->name('orders.pdf');
    Route::resource('orders', OrderController::class);

    Route::get('pos/order/{id}', [PosController::class, 'order'])->name('pos.order');
    Route::get('pos/kot/{id}', [PosController::class, 'kot'])->name('pos.kot');
    Route::resource('pos', PosController::class);

    Route::resource('kots', KotController::class);
    Route::get('kot/print/{id}/{kotPlaceid?}', [KotController::class, 'printkot'])->name('kot.print');

    Route::resource('customers', CustomerController::class);

    Route::resource('settings', RestaurantSettingController::class);


    Route::get('payments/export', fn() => Excel::download(new PaymentExport, 'payments-' . now()->toDateTimeString() . '.xlsx'))->name('payments.export');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/due', [PaymentController::class, 'due'])->name('payments.due');
    Route::get('payments/expenses', [PaymentController::class, 'expenses'])->name('payments.expenses');
    Route::get('payments/expenseCategory', [PaymentController::class, 'expenseCategory'])->name('payments.expenseCategory');

    Route::get('qr-codes', [QRCodeController::class, 'index'])->name('qrcodes.index');

    Route::resource('reservations', ReservationController::class);

    Route::prefix('reports')->group(function () {
        Route::get('item-report', [ReportController::class, 'itemReport'])->name('reports.item');
        Route::get('category-report', [ReportController::class, 'categoryReport'])->name('reports.category');
        Route::get('sales-report', [ReportController::class, 'salesReport'])->name('reports.sales');
        Route::get('expense-report', [ReportController::class, 'expenseReport'])->name('reports.expenseReports');
        Route::get('outstanding-payment-report', [ReportController::class, 'outstandingPaymentReport'])->name('reports.outstandingPayment');
        Route::get('expense-summary-report', [ReportController::class, 'expenseSummaryReport'])->name('reports.expensesummaryreport');
        Route::get('print-log', [ReportController::class, 'printLog'])->name('reports.printLog');
    });

    Route::resource('staff', StaffController::class);

    Route::resource('delivery-executives', DeliveryExecutiveController::class);
    Route::get('billing/upgrade-plan', [PlanController::class, 'index'])->name('pricing.plan');

    Route::get('/pusher/beams-auth', [DashboardController::class, 'beamAuth'])->name('beam_auth');

    Route::resource('waiter-requests', WaiterRequestController::class);

    Route::get('/customer-display', [\App\Http\Controllers\PosController::class, 'customerDisplay'])->name('customer.display');
});

Route::middleware(['auth', config('jetstream.auth_session'), 'verified', SuperAdmin::class])->group(function () {

    Route::name('superadmin.')->group(function () {
        Route::get('super-admin-dashboard', [DashboardController::class, 'superadmin'])->name('dashboard');

        Route::resource('restaurants', RestaurantController::class);

        Route::resource('restaurant-payments', RestaurantPaymentController::class);

        Route::resource('packages', PackageController::class);

        Route::resource('invoices', BillingController::class);


        Route::get('offline-plan', [BillingController::class, 'offlinePlanRequests'])->name('offline-plan-request');

        Route::get('users', [SuperadminSettingController::class, 'users'])->name('users.index');

        Route::resource('superadmin-settings', SuperadminSettingController::class);

        Route::post('app-update/deleteFile', [GlobalSettingController::class, 'deleteFile'])->name('app-update.deleteFile');
        Route::resource('app-update', GlobalSettingController::class);
        Route::post('custom-modules/verify-purchase', [CustomModuleController::class, 'verifyingModulePurchase'])->name('custom-modules.verify_purchase');
        Route::resource('custom-modules', CustomModuleController::class)->except(['update']);
        Route::put('custom-modules/{custom_module}', [CustomModuleController::class, 'update'])->withoutMiddleware('csrf')->name('custom-modules.update');

        Route::resource('landing-sites', LandingSiteController::class);
    });
});

Route::post('/webhook/billing-verify-webhook/{hash?}', [StripeWebhookController::class, 'verifyStripeWebhook'])->name('billing.verify-webhook');
Route::post('/webhook/save-razorpay-webhook/{hash?}', [RazorpayWebhookController::class, 'saveInvoices'])->name('billing.save_razorpay-webhook');
Route::post('/webhook/flutter-webhook/{hash}', [FlutterwavePaymentController::class, 'handleGatewayWebhook'])->name('flutterwave.webhook');
Route::match(['get', 'post'], '/success', [FlutterwavePaymentController::class, 'paymentMainSuccess'])->name('flutterwave.success');
Route::match(['get', 'post'], '/failed', [FlutterwavePaymentController::class, 'paymentFailed'])->name('flutterwave.failed');
Route::post('/webhook/save-flutterwave-webhook/{hash}', [FlutterwaveWebhookController::class, 'handleWebhook'])->name('billing.save-flutterwave-webhook');
Route::post('save-paypal-webhook/{hash}', [PaypalController::class, 'verifyBillingIPN'])->name('billing.save_paypal-webhook');
Route::post('payfast-notification/{id}', [PayFastWebhookController::class, 'saveInvoice'])->name('payfast-notification');
Route::post('/webhook/save-paystack-webhook/{hash}', [PaystackWebhookController::class, 'saveInvoices'])->name('billing.save-paystack-webhook');
Route::view('offline', 'offline');

Route::match(['get', 'post'], '/payfast/success', [PayfastPaymentController::class, 'paymentMainSuccess'])->name('payfast.success');
Route::match(['get', 'post'], '/payfast/failed', [PayfastPaymentController::class, 'paymentFailed'])->name('payfast.failed');
Route::post('/webhook/notify/{company}/{reference}', [PayfastPaymentController::class, 'payfastNotify'])->name('payfast.notify');

Route::post('/webhook/paypal-webhook/{hash}', [PaypalPaymentController::class, 'handleGatewayWebhook'])->name('paypal.webhook');
Route::get('paypal/success', [PaypalPaymentController::class, 'success'])->name('paypal.success');
Route::get('paypal/cancel', [PaypalPaymentController::class, 'cancel'])->name('paypal.cancel');
Route::match(['get', 'post'], '/success', [PaystackPaymentController::class, 'paymentMainSuccess'])->name('paystack.success');
Route::post('/webhook/paystack-webhook/{hash}', [PaystackPaymentController::class, 'handleGatewayWebhook'])->name('paystack.webhook');
Route::match(['get', 'post'], '/failed', [PaystackPaymentController::class, 'paymentFailed'])->name('paystack.failed');

Route::post('/webhook/xendit-webhook/{hash}', [XenditPaymentController::class, 'handleGatewayWebhook'])->name('xendit.webhook');
Route::match(['get', 'post'], '/xendit/success', [XenditPaymentController::class, 'paymentMainSuccess'])->name('xendit.success');
Route::match(['get', 'post'], '/xendit/failed', [XenditPaymentController::class, 'paymentFailed'])->name('xendit.failed');


Route::get('/receipt/{id}/preview', [ViewPngController::class, 'preview']); // shows the view to capture
Route::get('/kot/{id}/preview/{kotPlaceid?}', [ViewPngController::class, 'previewKot'])->name('kot.preview'); // shows KOT view to capture

Route::post('/kot/png', [ViewPngController::class, 'storeKot'])->name('kot.png.store'); // saves KOT PNG
Route::post('/order/png', [ViewPngController::class, 'storeOrder'])->name('order.png.store'); // saves Order PNG
