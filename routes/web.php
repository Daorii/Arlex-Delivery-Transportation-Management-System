<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SIPARequestController;
use App\Http\Controllers\SipadetailController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\TransportOrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportsAndAnalyticsController;
use App\Http\Controllers\TripClientController;
use App\Http\Controllers\TripDispatchController;
use App\Http\Controllers\DriverCommissionController;
use App\Http\Controllers\DriverDispatchController;
use App\Http\Controllers\DriverTripController;
use App\Http\Controllers\DashboardController;


use App\Models\Driver;

// Clients CRUD
Route::resource('clients', ClientController::class)->except(['show']);

// Trucks and Drivers pages
Route::view('/trucks', 'TD.trucks')->name('trucks');
Route::view('/drivers', 'TD.drivers')->name('drivers');

//Billing 
Route::get('/billings', [BillingController::class, 'index'])->name('billing.index');
Route::post('/billing/generate', [BillingController::class, 'generate'])->name('billing.generate');


// SIPA Requests
Route::get('/sipa-requests', [SIPARequestController::class, 'index'])->name('sipa-requests');
Route::get('/sipa-request-client/{clientId}', [SIPARequestController::class, 'showClientRequests'])->name('sipa-request-client');
Route::post('/sipa-requests/store', [SIPARequestController::class, 'store'])->name('sipa.store');


// SIPA Details
Route::resource('sipadetails', SipadetailController::class);
Route::get('/sipadetails/{sipaId}', [\App\Http\Controllers\SipadetailController::class, 'show']);

//DRIVERS
Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');

// Archive Driver Routes
Route::get('/drivers/archived', [DriverController::class, 'archived'])->name('drivers.archived');
Route::post('/drivers/{id}/archive', [DriverController::class, 'archive'])->name('drivers.archive');
Route::post('/drivers/{id}/restore', [DriverController::class, 'restore'])->name('drivers.restore');


//TRUCKS
Route::get('/trucks', [TruckController::class, 'index'])->name('trucks.index');
Route::post('/trucks', [TruckController::class, 'store'])->name('trucks.store');
Route::put('/trucks/{truck}', [TruckController::class, 'update'])->name('trucks.update');
Route::delete('/trucks/{truck}', [TruckController::class, 'destroy'])->name('trucks.destroy');

// Archive Truck Routes
Route::get('/trucks/archived', [TruckController::class, 'archived'])->name('trucks.archived');
Route::post('/trucks/{id}/archive', [TruckController::class, 'archive'])->name('trucks.archive');
Route::post('/trucks/{id}/restore', [TruckController::class, 'restore'])->name('trucks.restore');



//SIPA DETAILS FIXXX KAY DI MAKA EDIT
Route::put('/sipadetails/{rate}', [SipadetailController::class, 'update']);
Route::put('/sipadetails/{sipadetail}', [SipadetailController::class, 'update']);

//PARA SA DELETE 
Route::delete('/sipadetails/{sipadetail}', [SipadetailController::class, 'destroy']);

//EDIT A SIPA REF
Route::put('/sipa-requests/{id}', [SIPARequestController::class, 'update']);

// Driver dashboard
Route::get('/driver/dashboard', [DriverController::class, 'dashboard'])->name('driver.dashboard');

// -------------------- DRIVER PAGES -------------------- //
Route::view('/driver/profile', 'driver.profile')->name('driver.profile');
Route::view('/driver/trips', 'driver.trips_details')->name('driver.trips'); // Trips overview page
Route::view('/driver/dispatches', 'driver.dispatches')->name('driver.dispatches');


//DISPATCHES
Route::post('/dispatch', [DispatchController::class, 'store']);
Route::get('/dispatch/sipa/{sipaId}', [DispatchController::class,
'getDispatchesBySipa']);
Route::put('/dispatch/{dispatchId}', [DispatchController::class, 'update']);
Route::delete('/dispatch/{dispatchId}', [DispatchController::class, 'destroy']);
Route::get('/dispatch/search-drivers', [DispatchController::class, 'searchDrivers']);
Route::get('/dispatch/available-trucks', [DispatchController::class, 'getAvailableTrucks']);


//LOGINN SA DRIVERS
Route::get('/', fn() => view('welcome'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Logout route
Route::get('/logout', function () {
    // Clear all sessions
    session()->flush();

    // Redirect to login page
    return redirect()->route('login');
})->name('logout');

//DRIVER LOGOUT
Route::post('/logout', function () {
    session()->flush();
    return redirect()->route('login');
})->name('logout');



//DRIVER PROFILE
Route::get('/driver/profile', function () {
    $driverId = session('driver_id');

    if (!$driverId) {
        return redirect('/'); // redirect if not logged in
    }

    $driver = Driver::find($driverId); // get the driver record

    return view('driver.profile', compact('driver'));
})->name('driver.profile');



// Show the Transport Orders page (table + modal)
Route::get('/transport-orders', [TransportOrderController::class, 'index'])
    ->name('transport.orders');

// Store new Transport Order from the modal form
Route::post('/transport-orders', [TransportOrderController::class, 'store'])
    ->name('transport.orders.store');

// Optional: If you plan to approve/decline later, keep these
Route::post('/transport-orders/{to}/approve', [TransportOrderController::class, 'approve'])
    ->name('transport.orders.approve');

Route::post('/transport-orders/{to}/decline', [TransportOrderController::class, 'decline'])
    ->name('transport.orders.decline');

Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
Route::get('/reports-analytics', [ReportsAndAnalyticsController::class, 'index'])
    ->name('reports.analytics');


// TripClient
Route::get('/trip-client', [TripClientController::class, 'index'])->name('TD.TripClient');

// Redirect to TripDispatch when a client is clicked
Route::get('/trip-client/company/{clientId}', [TripDispatchController::class, 'show']);

// Page: Driver Commission (compute commissions)
Route::get('/td/driver-commission', [DriverCommissionController::class, 'driverCommission'])
    ->name('td.driver-commission');
Route::get('/commissions/driver-weeks/{driverId}', [DriverCommissionController::class, 'getDriverWeeklyPeriods']);


// Group all AJAX + commission record routes
Route::prefix('commissions')->name('commissions.')->group(function () {

    // Fetch approved EIRs
    Route::post('/fetch-approved', [DriverCommissionController::class, 'fetchApproved'])
        ->name('fetch-approved');

    // Save commission
    Route::post('/save', [DriverCommissionController::class, 'saveCommission'])
        ->name('save');

    // Commission Records Page
    Route::get('/records', [DriverCommissionController::class, 'records'])
        ->name('records');
    Route::get('/commissions/client-drivers/{clientId}', [DriverCommissionController::class, 'getClientDrivers']);

    // Fetch commissions for a specific driver (used in modal)
    Route::get('/view-driver/{driverId}', 
        [DriverCommissionController::class, 'viewDriverCommissions'])
        ->name('view-driver');

    // Update commission status
    Route::post('/update-status/{commissionId}', 
        [DriverCommissionController::class, 'updateStatus'])
        ->name('update-status');

    // Release all pending commissions for driver
    Route::post('/release-all/{driverId}', 
        [DriverCommissionController::class, 'releaseAllPendingForDriver'])
        ->name('release-all');
});


//TRIPS SA DRIVER
Route::get('/driver/dispatches', [DriverDispatchController::class, 'index'])->name('driver.dispatches');

Route::get('/driver/trip-details/{dispatch_id}', [DriverTripController::class, 'show'])->name('driver.trip.details');
Route::post('/driver/trip-details/store', [DriverTripController::class, 'store'])->name('driver.trip.store');

//ADMIN SIDE SA TRIPS
Route::post('/trip-dispatch/save-review', [TripDispatchController::class, 'saveReview'])->name('trip.dispatch.saveReview');

//BILLING NI
Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
Route::prefix('billing')->name('billing.')->group(function () {
    Route::get('/records', [BillingController::class, 'records'])->name('records');
});
Route::get('/billing/get-client-sipas/{clientId}', [BillingController::class, 'getClientSipas']);

Route::post('/billing/generate', [BillingController::class, 'generate'])->name('billing.generate');
Route::post('/billing/fetch-sipa', [BillingController::class, 'fetchSipaDetails'])->name('billing.fetchSipa');
Route::post('/billing/save', [BillingController::class, 'save'])->name('billing.save');



//TRANSPORT ORDER
Route::get('/transport-orders', [TransportOrderController::class, 'index'])->name('transport.orders');
Route::post('/transport-orders/store', [TransportOrderController::class, 'store'])->name('transport.orders.store');
Route::get('/transport-orders/search-sipa', [TransportOrderController::class, 'searchSipa'])->name('transport.orders.searchSipa');
Route::get('/transport-orders/sipa-details/{sipaRefNo}', [TransportOrderController::class, 'getSipaDetails'])->name('transport.orders.getSipaDetails');

//REAL DASHBOARD DATA!
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//BILLING RECORDS!
Route::get('/billing/view/{id}', [BillingController::class, 'view'])->name('billing.view');
Route::put('/billing/update-status/{id}', [BillingController::class, 'updateStatus'])->name('billing.updateStatus');

//TRANSPORT ORDER NA UPDATED NI
Route::get('/transport-orders', [TransportOrderController::class, 'index'])->name('transport-orders.index');
Route::get('/transport-orders/search-soa', [TransportOrderController::class, 'searchSoa'])->name('transport-orders.search-soa');
Route::get('/transport-orders/soa-details/{billing_id}', [TransportOrderController::class, 'getSoaDetails'])->name('transport-orders.soa-details');
Route::post('/transport-orders/store', [TransportOrderController::class, 'store'])->name('transport-orders.store');
Route::put('/transport-orders/update-status', [TransportOrderController::class, 'updateStatus'])->name('transport-orders.update-status');


// Invoice Routes
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/invoices/transport-orders', [InvoiceController::class, 'getTransportOrders'])->name('invoices.transport-orders');
Route::get('/invoices/view/{id}', [InvoiceController::class, 'view'])->name('invoices.view');
Route::put('/invoices/update-status/{id}', [InvoiceController::class, 'updateStatus'])->name('invoices.update-status');

// Payment Routes
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/payments/search-invoices', [PaymentController::class, 'searchInvoices'])->name('payments.search-invoices');
Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
Route::put('/payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');

//EDIT SA DRIVERRR
Route::get('/driver/profile', [DriverController::class, 'profile'])->name('driver.profile');
Route::put('/driver/profile', [DriverController::class, 'updateProfile'])->name('driver.profile.update');

// ✅ NO MIDDLEWARE NEEDED
Route::get('/driver/commissions', [DriverCommissionController::class, 'driverCommissionsPage'])
    ->name('driver.commission');

Route::get('/driver/fetch-commissions', [DriverCommissionController::class, 'fetchDriverCommissions'])
    ->name('driver.fetchCommissions');

//EDIT SA SIPA REF
Route::put('/sipa-requests/{id}', [SIPARequestController::class,
'update'])->name('sipa.update');

//ARCHIVED CLIENT ROUTES
Route::get('/clients/archived', [ClientController::class, 'archived'])->name('clients.archived');
Route::post('/clients/{id}/archive', [ClientController::class, 'archive'])->name('clients.archive');
Route::post('/clients/{id}/restore', [ClientController::class, 'restore'])->name('clients.restore');

//ARCHIVED SIPA REQ
Route::get('/sipa-request-client/{clientId}', [SIPARequestController::class, 'showClientRequests'])->name('sipa.client.requests');

Route::get('/sipa-request-client/{clientId}/archived', [SIPARequestController::class, 'archived'])->name('sipa.archived');
Route::post('/sipa-requests/{id}/archive', [SIPARequestController::class, 'archive'])->name('sipa.archive');
Route::post('/sipa-requests/{id}/restore', [SIPARequestController::class, 'restore'])->name('sipa.restore');
Route::delete('/sipa-requests/{id}', [SIPARequestController::class, 'destroy'])->name('sipa.destroy');

// Billing Archive Routes
Route::get('/billing/archived', [BillingController::class, 'archived'])->name('billing.archived');
Route::post('/billing/{id}/archive', [BillingController::class, 'archive'])->name('billing.archive');
Route::post('/billing/{id}/restore', [BillingController::class, 'restore'])->name('billing.restore');
Route::delete('/billing/{id}', [BillingController::class, 'destroyBilling'])->name('billing.destroy');

// Transport Order Archive Routes
Route::get('/transport-orders/archived', [TransportOrderController::class, 'archived'])->name('transport-orders.archived');
Route::post('/transport-orders/{toRefNo}/archive', [TransportOrderController::class, 'archive'])->name('transport-orders.archive');
Route::post('/transport-orders/{toRefNo}/restore', [TransportOrderController::class, 'restore'])->name('transport-orders.restore');
Route::delete('/transport-orders/{toRefNo}', [TransportOrderController::class, 'destroy'])->name('transport-orders.destroy');

// Invoice Archive Routes
Route::get('/invoices/archived', [InvoiceController::class, 'archived'])->name('invoices.archived');
Route::post('/invoices/{invoiceId}/archive', [InvoiceController::class, 'archive'])->name('invoices.archive');
Route::post('/invoices/{invoiceId}/restore', [InvoiceController::class, 'restore'])->name('invoices.restore');
Route::delete('/invoices/{invoiceId}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

// Payment Archive Routes
Route::get('/payments/archived', [PaymentController::class, 'archived'])->name('payments.archived');
Route::post('/payments/{paymentId}/archive', [PaymentController::class, 'archive'])->name('payments.archive');
Route::post('/payments/{paymentId}/restore', [PaymentController::class, 'restore'])->name('payments.restore');

// Trip Dispatch Archive Routes
Route::post('/trip-dispatch/{id}/archive', [TripDispatchController::class, 'archive'])->name('trip-dispatch.archive');
Route::post('/trip-dispatch/{id}/restore', [TripDispatchController::class, 'restore'])->name('trip-dispatch.restore');
Route::get('/trip-dispatch/{clientId}/archived', [TripDispatchController::class, 'archived'])->name('trip-dispatch.archived');
Route::delete('/trip-dispatch/{id}', [TripDispatchController::class, 'destroy'])->name('trip-dispatch.destroy');