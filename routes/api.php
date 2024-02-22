<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::get('refresh', 'AuthController@refresh');

    Route::group(['middleware' => 'auth:api'], function(){
        Route::get('user', 'AuthController@user');
        Route::post('logout', 'AuthController@logout');
    });
});

Route::group(['middleware' => 'auth:api'], function(){
    // Users
    Route::get('customer', 'UserController@indexCustomer')->middleware('isAdmin');
    Route::get('customer/{id}', 'UserController@showCustomer')->middleware('isAdmin');
    Route::put('customer/{id}', 'UserController@updateCustomer')->middleware('isAdmin');
    Route::post('customer', 'UserController@createCustomer');
    Route::delete('customer', 'UserController@deleteCustomer');
    Route::post('donate', 'UserController@donateCustomer');
    Route::get('confirm/password/{code}', 'UserController@confirmEmailLogin');
    // Admin
    Route::post('admin', 'UserController@createAdmin');
    Route::get('admin', 'UserController@listAdmin');
    Route::get('admin/{id}', 'UserController@showAdmin');
    Route::delete('admin', 'UserController@deleteAdmin');
    Route::put('admin/{id}', 'UserController@updateAdmin');
    // Contact person
    Route::post('contact-person', 'UserController@createContactPerson');
    Route::get('contact-person', 'UserController@listContactPerson');
    Route::put('contact-person/{id}', 'UserController@updateContactPerson');
    Route::delete('contact-person', 'UserController@deleteContactPerson');
    //Niche
    Route::post('niche', 'NicheController@store');
    Route::get('niche', 'NicheController@index');
    Route::put('niche/{id}', 'NicheController@update');
    Route::delete('niche', 'NicheController@delete');
    Route::get('niche/{id}', 'NicheController@detail');
    Route::post('export-niche', 'NicheController@exportListNiches');
    Route::get('niche-booking', 'NicheController@getNicheNotBooking');
    Route::post('import-niche', 'NicheController@importListNiches');
    Route::post('duration-niches', 'NicheController@store_duration');
    Route::get('duration-niches', 'NicheController@list_duration');
    Route::put('duration-niches/{id}', 'NicheController@update_duration');
    Route::delete('duration-niches', 'NicheController@deleteDuration');
    Route::get('list-duration-niches', 'NicheController@listDurationNiche');
    Route::get('update-status-niches', 'NicheController@updateStatusNiche');
    Route::post('bulk-action', 'NicheController@bulkActionUpdateNiche');
    Route::get('all-id-niche', 'NicheController@allIDNiche');

    //memorial-room
    Route::post('memorial-room', 'MemorialRoomController@store');
    Route::get('memorial-room', 'MemorialRoomController@index');
    Route::delete('memorial-room', 'MemorialRoomController@delete');
    Route::put('memorial-room/{id}', 'MemorialRoomController@update');
    Route::get('memorial-room/{id}', 'MemorialRoomController@detail');
    Route::get('list-room', 'MemorialRoomController@listRoomNotBooking');
    Route::get('booking-log/{id}', "MemorialRoomController@getBookingLog");
    Route::post('import-room', 'MemorialRoomController@importListRoom');
    Route::post('export-booking-log', 'MemorialRoomController@exportBookingLog');

    // other
    Route::post('other', 'OtherController@store');
    Route::get('other', 'OtherController@index');
    Route::delete('other', 'OtherController@delete');
    Route::put('other/{id}', 'OtherController@update');
    Route::get('other/{id}', 'OtherController@detail');
    Route::get('other-by-contractor', 'OtherController@listOtherByContractor');
    Route::post('service-type', 'OtherController@createServiceType');
    Route::get('service-type', 'OtherController@listServiceType');
    Route::put('service-type/{id}', 'OtherController@updateServiceType');
    Route::delete('service-type', 'OtherController@deleteServiceType');
    Route::get('list-service', 'OtherController@getServiceAndChild');
    Route::get('list-service-by-type/{id}', 'OtherController@getListServiceByType');
    Route::get('list-other-category', 'OtherController@getListOtherCategory');
    
    // directors
    Route::post('directors', 'DirectorsController@createDirectors');
    Route::get('directors', 'DirectorsController@indexDirectors');
    Route::get('directors/{id}', 'DirectorsController@showDirectors');
    Route::put('directors/{id}', 'DirectorsController@updateDirectors');
    Route::delete('directors', 'DirectorsController@deleteDirectors');
    Route::get('list-director', 'DirectorsController@getAllFuneralDirector');
    // contractor
    Route::post('contractor', 'ContractorController@createContractor');
    Route::get('contractor', 'ContractorController@indexContractor');
    Route::get('contractor/{id}', 'ContractorController@showContractor');
    Route::put('contractor/{id}', 'ContractorController@updateContractor');
    Route::delete('contractor', 'ContractorController@deleteContractor');
    Route::get('list-contractor', 'ContractorController@getAllContractor');
    // Booking
    Route::post('booking', 'BookingController@createBooking');
    Route::delete('booking', 'BookingController@deleteBooking');
    Route::get('service-booking/{id}', 'BookingController@showServiceBooking');
    Route::put('booking/{id}', 'BookingController@updateBooking');
    Route::post('room-booking/{id}', 'BookingController@updateBooking');
    Route::get('booking', 'BookingController@getListBooking');
    Route::delete('service', 'BookingController@deleteService');
    Route::get('extension', 'BookingController@extensionNiche');
    Route::get('get-niche-extension', 'BookingController@getNichesExtension');
    Route::get('extension-mutiple-niches', 'BookingController@extensionMutipleNiche');
    Route::put('update-status-booking/{id}', 'BookingController@updateStatusBooking');
    Route::post('line-no', 'BookingController@getLineNoBooking');
    // Reports
    Route::get('report', 'ReportController@listReport');
    Route::post('report/{id}', 'ReportController@updateFile');
    Route::get('report/{id}', 'ReportController@showReportFile');
    Route::delete('report', 'ReportController@deleteFile');
    Route::post('report', 'ReportController@create');
    // Route::post('generate-report', 'ReportController@generateCollections');
    // GST Rate
    Route::get('gst-rate', 'GSTRateController@index');
    Route::post('gst-rate', 'GSTRateController@create');
    Route::get('history-gst-rate', 'GSTRateController@getHistory');
    // Get profile
    Route::post('/me', 'AuthController@getUserInfo');
 
    // Finance - Booking General
    Route::get('booking-general', 'BookingGeneralController@listBookingGeneral');
    Route::get('booking-general/{id}', 'BookingGeneralController@showBookingGeneral');
    Route::post('status-booking-general', 'BookingGeneralController@listStatusBooking');
    Route::post('booking-general/{id}', 'BookingGeneralController@updateBookingGeneral');
    // Finance - Sale Agreement
    Route::post('sale-agreement', 'SaleAgreementController@createSaleAgreement');
    // Route::get('sale-agreement/{id}', 'SaleAgreementController@getDetailSaleAgreement');
    Route::post('amount-count', 'SaleAgreementController@amountCount');
    Route::get('sale-agreement', 'SaleAgreementController@listSaleAgreement');
    Route::delete('sale-agreement', 'SaleAgreementController@delete');
    Route::post('amount-donate', 'SaleAgreementController@amountDonate');
    Route::post('total-sale-agreement', 'SaleAgreementController@handleTotalSaleAgreement');
    //Remarks
    Route::get('remarks', 'RemarksController@getListRemarks');
    Route::post('remarks', 'RemarksController@store');
    Route::get('remarks/{id}', 'RemarksController@detail');
    Route::post('remarks/{id}', 'RemarksController@update');
    Route::get('download-remarks/{id}', 'RemarksController@downloadRemarks');
    // Finance - Invoices
    Route::post('invoices', 'InvoicesController@createInvoices');
    Route::get('invoices', 'InvoicesController@listInvoice');
    Route::delete('invoices', 'InvoicesController@delete');
    // Route::get('invoices/{id}', 'InvoicesController@getDetailInvoices');
    Route::post('save-signature-invoice/{id}', 'InvoicesController@saveSignatureInvoices');
    Route::post('total-invoice', 'InvoicesController@getTotalInvoice');
    // Attachments
    Route::post('attachment', 'AttachmentController@saveAttachment');
    Route::get('attachment', 'AttachmentController@getAttachment');
    Route::delete('attachment', 'AttachmentController@deleteAttachment');
    Route::get('download-attachment', 'AttachmentController@downloadAttachment');
    // Finance - Payment
    Route::post('payment', 'PaymentController@createPayment');
    Route::get('payment', 'PaymentController@listPayment');
    // Route::get('payment/{id}', 'PaymentController@getDetailPayment');
    Route::post('update-payment/{id}', 'PaymentController@updatePayment');
    Route::delete('payment', 'PaymentController@deletePayment');
    Route::get('get-donation/{id}', 'PaymentController@getDonation');

    Route::get('discount-niche', 'DiscountController@index');
    Route::post('discount-niche', 'DiscountController@store');
    Route::put('discount-niche/{id}', 'DiscountController@update');
    Route::delete('discount-niche', 'DiscountController@delete');
    Route::get('discount-detail/{id}', 'DiscountController@detail');
    Route::post('import-discount', 'DiscountController@importDiscount');
    Route::post('import-price-niches', 'DiscountController@importPriceNiches');

    // Niches Reserved
    Route::post('niche-reserved', 'NicheReservedController@create');
    Route::put('niche-reserved/{id}', 'NicheReservedController@update');
    Route::delete('niche-reserved', 'NicheReservedController@delete');
    Route::get('niche-reserved', 'NicheReservedController@index');
    Route::get('niche-reserved/{id}', 'NicheReservedController@show');

    Route::get('partial-payment/{id}', 'PartialPaymentController@show');
    Route::post('partial-payment', 'PartialPaymentController@createPartialPayment');
    Route::get('send-partial-payment/{id}', 'PartialPaymentController@sendEmailPartialPayment');
    Route::get('print-partial-payment/{id}', 'PartialPaymentController@printPartialPayment');
    Route::post('update-partial-payment/{id}', 'PartialPaymentController@update');
    Route::delete('delete-partial-payment/{id}', 'PartialPaymentController@destroy');

});

Route::post('reference', 'BaseController@store');
Route::get('reference', 'ReferenceController@index');

Route::get('sale-agreement/{id}', 'SaleAgreementController@getDetailSaleAgreement');
Route::get('invoices/{id}', 'InvoicesController@getDetailInvoices');
Route::get('payment/{id}', 'PaymentController@getDetailPayment');
//Đang test 
// Route::put('niche/{id}', 'NicheController@update');
// Route::get('ordinal-number', 'UserController@ordinalNumber');
Route::get('export', 'ExportController@export');
Route::post('import-customer', 'UserController@importListCustomer');
Route::post('import-booking', 'BookingController@importBooking')->name('import-booking');
Route::post('import-ocupied', 'BookingController@importOccupied');
Route::get('update-salutation', 'UserController@updateSalutation');
Route::post('save-signature/{id}', 'SaleAgreementController@saveSignature');
Route::get('make-invoices/{id}', 'InvoicesController@exportInvoice');
Route::get('update-gst', 'InvoicesController@updateGST');
Route::get('make-payment/{id}', 'PaymentController@exportPayment');
Route::get('make-niche-licence/{id}', 'NicheController@exportNicheLicence');
Route::get('make-sale-agreement/{id}', 'SaleAgreementController@printSaleAgreement');
Route::get('print-document/{id}', 'SaleAgreementController@printDocument');
Route::get('send-document/{id}', 'SaleAgreementController@sendEmailAttachFileToClient');
Route::post('niches-total', 'BookingController@totalNichesRenew');

Route::get('send-invoice/{id}', 'InvoicesController@sendInvoiceToClient');
Route::get('send-payment/{id}', 'PaymentController@sendEmailPayment');

Route::get('view-test', function () {
    return view('import-excel.test');
});
Route::get('view-test-export', function () {
    return view('exports.niche-licence-end');
});

Route::get('view-test-email', 'UserController@testView');


Route::get('update-status', 'BookingController@updateStatusServiceNiches');

Route::get('list-discount/{id}', 'SaleAgreementController@listDiscount');

Route::get('update-list-discount', 'SaleAgreementController@updateListDiscountForBooking');

Route::post('import-services', 'OtherController@importServices');

Route::post('forget-password', 'UserController@userForgotPassword');

Route::post('import-update-booking', 'BookingController@importUpdateBooking');

Route::post('update-all-niche', 'NicheController@updateAllNiche');

Route::post('import-address', 'AddressController@importAddress');
Route::get('address', 'AddressController@getListAddress');
Route::get('detail-address/{id}', 'AddressController@deatilAddress');
Route::put('address/{id}', 'AddressController@updateAddress');
Route::delete('address', 'AddressController@deleteAddress');
Route::get('find-address', 'AddressController@findAddress');
Route::post('address', 'AddressController@createAddress');
Route::get('list-agreement', 'InvoicesController@getListAgreement');
Route::post('add-agreement', 'InvoicesController@addSaleAgreement');
Route::delete('delete-agreement-line', 'InvoicesController@deleteSaleAgreement');
Route::get('generate-report/{id}', 'ReportController@generateCollections');
Route::post('cancel-invoices', 'InvoicesController@cancelInvoice');
Route::get('dowload-zip-remarks', 'BookingController@downloadZip');
Route::get('list-booking-add-invoice', 'InvoicesController@listBooking');
Route::post('add-invoices', 'InvoicesController@addInvoices');
Route::get('make-info-sale-agreement/{id}', 'SaleAgreementController@infoSaleAgreement');
Route::post('import-niche-new-entries', 'BookingController@importNicheNewEntries');
Route::post('import-data-booking', 'BookingController@importDataBooking');




