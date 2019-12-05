<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
});
//these function is for frontend route
Route::get('/', ['as' => 'index', 'uses' => 'LoginFrontController@ShowLogin']);
Route::post('do_login', 'LoginFrontController@DoLogin');

Route::get('home', 'FrontEndController@ShowingBarcode');
Route::post('home', 'FrontEndController@ShowingBarcode');
Route::post('check_barcode', 'FrontEndController@CheckingBarcode');
Route::get('logout', 'LoginFrontController@logout');
Route::get('rescan', 'FrontEndController@RescanList');

Route::group(['prefix' => 'frontend', 'as' => 'frontend.'], function () {

    Route::post("scan", "FrontEndController@scan_ticket");

    Route::post("authorize", "FrontEndController@ScanningBarcode");
    Route::get("authorize", "FrontEndController@GetAuthorize");

    Route::post("deauthorize", "FrontEndController@Deauthorize");

    Route::post("rescan_ticket", "FrontEndController@RescanTicket");
});

Route::get("get_barcode/{code}", 'BarcodeController@index');

//these function is for backend route
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {

    //these route for login page
    Route::get('/', ['as' => 'index', 'uses' => 'Admin\BackendController@ShowLoginForm']);

    Route::post('do_login', "Admin\BackendController@DoLogin");

    Route::get('logout', "Admin\BackendController@logout");

    Route::get('welcome', "Admin\BackendController@welcome");

    //this route for dashboard
    Route::get('dashboard', 'Admin\DashboardController@index');

    //this route for setting
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {

        Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\StationController@index']);

        //this route for all user group functions
        Route::group(['prefix' => 'group', 'as' => 'group.'], function () {

            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Group\UserGroupController@index']);
            Route::get("delete/{id}", "Admin\Setting\Group\UserGroupController@delete");
            Route::post("save", "Admin\Setting\Group\UserGroupController@store");
        });

        //this route for all users functions
        Route::group(['prefix' => 'users', 'as' => 'users.'], function () {

            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\User\UserController@show']);
            Route::post("delete", "Admin\Setting\User\UserController@delete");
            Route::post("save", "Admin\Setting\User\UserController@save");
            Route::get("profile", "Admin\Setting\User\UserController@profile_user");
            Route::post("change_profile", "Admin\Setting\User\UserController@upload");

            Route::group(['prefix' => 'round', 'as' => 'round.'], function () {
                Route::get('list/{id}', 'Admin\Setting\User\UserRoundTripController@index');
                Route::post('save', 'Admin\Setting\User\UserRoundTripController@save');
                Route::post('delete/{id}', 'Admin\Setting\User\UserRoundTripController@delete');
            });

            Route::group(['prefix' => 'group', 'as' => 'group.'], function () {
                Route::get('list/{id}', 'Admin\Setting\Group\UserGroupController@list_group');
                Route::post('save', 'Admin\Setting\Group\UserGroupController@save_group');
                Route::post('delete/{id}', 'Admin\Setting\Group\UserGroupController@delete_group');
            });
        });

        //All this function is for station feature
        Route::group(['prefix' => 'station', 'as' => 'station.'], function () {

            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Station\StationController@station']);
            Route::post("save", "Admin\Setting\Station\StationController@save_station");
            Route::post("delete", "Admin\Setting\Station\StationController@delete_station");
        });

        //All this function is for grade feature
        Route::group(['prefix' => 'grade', 'as' => 'grade.'], function () {

            Route::post("get", "Admin\Setting\Station\GradeController@get_grade");
            Route::post("delete", "Admin\Setting\Station\GradeController@delete_grade");
            Route::post("save", "Admin\Setting\Station\GradeController@save_grade");
        });

        //All this function is for tank feature
        Route::group(['prefix' => 'tank', 'as' => 'tank.'], function () {

            Route::post("get", "Admin\Setting\Station\TankController@get_tank");
            Route::post("delete", "Admin\Setting\Station\TankController@delete_tank");
            Route::post("save", "Admin\Setting\Station\TankController@save_tank");
        });

        //All this function is for pump feature
        Route::group(['prefix' => 'pump', 'as' => 'pump.'], function () {

            Route::post("get", "Admin\Setting\Station\PumpController@get_pump");
            Route::post("delete", "Admin\Setting\Station\PumpController@delete_pump");
            Route::post("save", "Admin\Setting\Station\PumpController@save_pump");
            Route::post("list_pump", "Admin\Setting\Station\PumpController@list_pump");
        });

        //All this function is for nozzle feature
        Route::group(['prefix' => 'nozzle', 'as' => 'nozzle.'], function () {

            Route::post("get", "Admin\Setting\Station\NozzleController@get_nozzle");
            Route::post("delete", "Admin\Setting\Station\NozzleController@delete_nozzle");
            Route::post("save", "Admin\Setting\Station\NozzleController@save_nozzle");
        });

        //All these routes for user permission
        Route::group(['prefix' => 'permission', 'as' => 'permission.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\User\UserPermissionController@show']);
            Route::post('get', 'Admin\Setting\User\UserPermissionController@get');
            Route::post('save', 'Admin\Setting\User\UserPermissionController@save');
            Route::post('delete', 'Admin\Setting\User\UserPermissionController@delete');
        });

        //all these routes for customer
        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Customer\CustomerController@index']);
            Route::post('save', 'Admin\Setting\Customer\CustomerController@save');
            Route::post('delete', 'Admin\Setting\Customer\CustomerController@delete');
            Route::get('export', 'Admin\Setting\Customer\CustomerController@export');
            Route::post('import', 'Admin\Setting\Customer\CustomerController@import');
            Route::get('download', 'Admin\Setting\Customer\CustomerController@download');
        });

        //all these routes for reason
        Route::group(['prefix' => 'reason', 'as' => 'reason.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Reason\ReasonController@index']);
            Route::post('save', 'Admin\Setting\Reason\ReasonController@save');
            Route::post('delete', 'Admin\Setting\Reason\ReasonController@delete');
            Route::get('export', 'Admin\Setting\Reason\ReasonController@export');
            Route::post('import', 'Admin\Setting\Reason\ReasonController@import');
            Route::get('download', 'Admin\Setting\Reason\ReasonController@download');
        });

        //all these routes for trailer
        Route::group(['prefix' => 'trailer', 'as' => 'trailer.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Trailer\TrailerController@index']);
            Route::post('save', 'Admin\Setting\Trailer\TrailerController@store');
            Route::post('delete', 'Admin\Setting\Trailer\TrailerController@destroy');
            Route::get('export', 'Admin\Setting\Trailer\TrailerController@export');
            Route::post('import', 'Admin\Setting\Trailer\TrailerController@import');
            Route::get('download', 'Admin\Setting\Trailer\TrailerController@download');
        });

        //all these routes for container
        Route::group(['prefix' => 'container', 'as' => 'container.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Container\ContainerController@index']);
            Route::post('save', 'Admin\Setting\Container\ContainerController@store');
            Route::post('delete', 'Admin\Setting\Container\ContainerController@destroy');
            Route::get('export', 'Admin\Setting\Container\ContainerController@export');
            Route::post('import', 'Admin\Setting\Container\ContainerController@import');
            Route::get('download', 'Admin\Setting\Container\ContainerController@download');
            Route::post('get_container', 'Admin\Setting\Container\ContainerController@get_container');
            Route::get('get', 'Admin\Setting\Container\ContainerController@get');
        });

        //all these routes for fleet
        Route::group(['prefix' => 'fleet', 'as' => 'fleet.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Fleet\FleetController@index']);
            Route::post('save', 'Admin\Setting\Fleet\FleetController@store');
            Route::post('delete', 'Admin\Setting\Fleet\FleetController@destroy');
            Route::get('export', 'Admin\Setting\Fleet\FleetController@export');
            Route::post('import', 'Admin\Setting\Fleet\FleetController@import');
            Route::get('download', 'Admin\Setting\Fleet\FleetController@download');

            //for fleet driver
            Route::post('fleet_driver', 'Admin\Setting\Fleet\FleetController@fleet_driver');
            Route::post('save_fleet_driver', 'Admin\Setting\Fleet\FleetController@save_fleet_driver');
            Route::post('delete_fleet_driver', 'Admin\Setting\Fleet\FleetController@delete_fleet_driver');

            Route::get('export_fleet_driver', 'Admin\Setting\Fleet\FleetController@export_fleet_driver');
            Route::get('download_fleet_driver', 'Admin\Setting\Fleet\FleetController@download_fleet_driver');
            Route::post('import_fleet_driver', 'Admin\Setting\Fleet\FleetController@import_fleet_driver');
        });

        //all these routes for driver
        Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Driver\DriverController@index']);
            Route::post('save', 'Admin\Setting\Driver\DriverController@store');
            Route::post('delete', 'Admin\Setting\Driver\DriverController@destroy');
            Route::get('export', 'Admin\Setting\Driver\DriverController@export');
            Route::post('import', 'Admin\Setting\Driver\DriverController@import');
            Route::get('download', 'Admin\Setting\Driver\DriverController@download');
        });

        //all these routes for destination
        Route::group(['prefix' => 'destination', 'as' => 'destination.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Destination\DestinationController@index']);
            Route::post('save', 'Admin\Setting\Destination\DestinationController@save');
            Route::post('delete', 'Admin\Setting\Destination\DestinationController@delete');
            Route::get('export', 'Admin\Setting\Destination\DestinationController@export');
            Route::post('import', 'Admin\Setting\Destination\DestinationController@import');
            Route::get('download', 'Admin\Setting\Destination\DestinationController@download');
        });

        //all these routes for destination
        Route::group(['prefix' => 'roundtrip', 'as' => 'roundtrip.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\RoundTrip\RoundTripController@index']);
            Route::post('save', 'Admin\Setting\RoundTrip\RoundTripController@store');
            Route::post('delete', 'Admin\Setting\RoundTrip\RoundTripController@destroy');
        });

        //all these routes for company
        Route::group(['prefix' => 'company', 'as' => 'company.'], function () {

            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Company\CompanyController@show']);
            Route::post('update', 'Admin\Setting\Company\CompanyController@edit');
        });

        //all these routes for supervisor
        Route::group(['prefix' => 'supervisor', 'as' => 'supervisor.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\Supervisor\SupervisorController@index']);
            Route::post('save', 'Admin\Setting\Supervisor\SupervisorController@save');
            Route::post('delete', 'Admin\Setting\Supervisor\SupervisorController@delete');
            Route::post('import', 'Admin\Setting\Supervisor\SupervisorController@import');
            Route::get('download', 'Admin\Setting\Supervisor\SupervisorController@download');
        });

        //all these routes for supervisor
        Route::group(['prefix' => 'teamleader', 'as' => 'teamleader.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\TeamLeader\TeamLeaderController@index']);
            Route::post('save', 'Admin\Setting\TeamLeader\TeamLeaderController@save');
            Route::post('delete', 'Admin\Setting\TeamLeader\TeamLeaderController@delete');
            Route::post('import', 'Admin\Setting\TeamLeader\TeamLeaderController@import');
            Route::get('download', 'Admin\Setting\TeamLeader\TeamLeaderController@download');
        });

        //all these routes for MT Pickup Depot
        Route::group(['prefix' => 'mtpickup', 'as' => 'mtpickup.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\MTPickup\MTPickUpController@index']);
            Route::post('save', 'Admin\Setting\MTPickup\MTPickUpController@save');
            Route::post('delete', 'Admin\Setting\MTPickup\MTPickUpController@delete');
            Route::post('import', 'Admin\Setting\MTPickup\MTPickUpController@import');
            Route::get('download', 'Admin\Setting\MTPickup\MTPickUpController@download');
        });

        //all these routes for MT Pickup Depot
        Route::group(['prefix' => 'paytrip', 'as' => 'paytrip.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\paytrip\PayTripController@index']);
            Route::post('save', 'Admin\Setting\PayTrip\PayTripController@save');
            Route::post('delete', 'Admin\Setting\PayTrip\PayTripController@delete');
            Route::post('import', 'Admin\Setting\PayTrip\PayTripController@import');
            Route::post('import_matrix', 'Admin\Setting\PayTrip\PayTripController@import_matrix');
            Route::post('save_matrix', 'Admin\Setting\PayTrip\PayTripController@save_matrix');
            Route::get('export', 'Admin\Setting\PayTrip\PayTripController@export');
            Route::get('download', 'Admin\Setting\PayTrip\PayTripController@download');
            Route::get('download_matrix', 'Admin\Setting\PayTrip\PayTripController@download_matrix');
        });

        //all these routes for advanced pay
        Route::group(['prefix' => 'advancepay', 'as' => 'advancepay.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\AdvancedPay\AdvancedPayController@index']);
            Route::post('save', 'Admin\Setting\AdvancedPay\AdvancedPayController@save');
            Route::post('save_matrix', 'Admin\Setting\AdvancedPay\AdvancedPayController@save_matrix');
            Route::post('delete', 'Admin\Setting\AdvancedPay\AdvancedPayController@delete');
            Route::post('import', 'Admin\Setting\AdvancedPay\AdvancedPayController@import');
            Route::post('import_matrix', 'Admin\Setting\AdvancedPay\AdvancedPayController@import_matrix');
            Route::get('download', 'Admin\Setting\AdvancedPay\AdvancedPayController@download');
            Route::get('download_matrix', 'Admin\Setting\AdvancedPay\AdvancedPayController@download_matrix');
        });

        //all these routes for account balance
        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Admin\Setting\AccountBalance\AccountBalanceController@index']);
            Route::post('save', 'Admin\Setting\AccountBalance\AccountBalanceController@save');
            Route::post('update', 'Admin\Setting\AccountBalance\AccountBalanceController@update');
        });

        //for print nozzle code
        Route::get("print_nozzle", "Admin\Setting\Station\NozzleController@print_nozzle");
        Route::post("print_nozzle", "Admin\Setting\Station\NozzleController@print_nozzle");
    });

    //this route for ticket
    Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {

        Route::get('/', ['as' => 'index', 'uses' => 'Admin\Ticket\TicketController@index']);
        Route::get('list', "Admin\Ticket\TicketController@show");
        Route::get('data', "Admin\Ticket\TicketController@data");
        Route::post('list', "Admin\Ticket\TicketController@show");
        Route::get('export', 'Admin\Ticket\TicketController@export');
        Route::post('extend', 'Admin\Ticket\TicketController@extend');
        Route::post('reverse', 'Admin\Ticket\TicketController@reverse');
        Route::get('create', "Admin\Ticket\TicketController@create");
        Route::post("save", "Admin\Ticket\TicketController@save");
        Route::post("clone-data", "Admin\Ticket\TicketController@CloneData");
        Route::post("get_feet", "Admin\Ticket\TicketController@GetFeet");
        Route::post("get_feet_by_number", "Admin\Ticket\TicketController@get_feet_by_number");
        Route::post("reprint", "Admin\Ticket\TicketController@RePrint");
        Route::get("testing/{id}", "Admin\Ticket\TicketController@Testing");
        Route::get("detail/{id}", "Admin\Ticket\TicketController@detail");
        Route::post("update_status", "Admin\Ticket\TicketController@update_status");
        Route::get("get_driver/{id}", "Admin\Ticket\TicketController@get_driver");
        Route::get("exist_driver/{id}", "Admin\Ticket\TicketController@exist_driver");
        Route::get("exist_trailer/{id}", "Admin\Ticket\TicketController@exist_trailer");
        Route::post("update_date", "Admin\Ticket\TicketController@update_date");
        Route::post("credit", "Admin\Ticket\TicketController@credit");
        Route::post("used_update", "Admin\Ticket\TicketController@used_update");
        Route::post("update_remark", "Admin\Ticket\TicketController@update_remark");
        //get data from for select 2
        Route::get("get_fleet", "Admin\Ticket\TicketController@get_fleet");
        Route::get("get_driver_by_group", "Admin\Ticket\TicketController@get_driver_by_group");
        Route::get("get_trailer", "Admin\Ticket\TicketController@get_trailer");
        Route::get("get_reason", "Admin\Ticket\TicketController@get_reason");
        Route::get("get_mtpickup", "Admin\Ticket\TicketController@get_mtpickup");
        Route::get("get_destination", "Admin\Ticket\TicketController@get_destination");
        Route::get("get_customer", "Admin\Ticket\TicketController@get_customer");
        Route::get("get_team_leader", "Admin\Ticket\TicketController@get_team_leader");
        Route::get("get_container", "Admin\Ticket\TicketController@get_container");
        Route::post("get_credit", "Admin\Ticket\TicketController@get_credit");
        Route::post("get_writeoff", "Admin\Ticket\TicketController@get_writeoff");
        Route::post("get_round_trip", "Admin\Ticket\TicketController@get_round_trip");
        Route::post('autocomplete', 'Admin\Ticket\TicketController@autocomplete');
        Route::post('getnote_paytrip', 'Admin\Ticket\TicketController@getnote_paytrip');
        Route::post('update', 'Admin\Ticket\TicketController@update');
        Route::get("edit/{id}", "Admin\Ticket\TicketController@EditTicket");
        Route::get("get_destination_by_id/{id}", "Admin\Ticket\TicketController@get_destination_by_id");
        Route::post("get_advance_pay", "Admin\Ticket\TicketController@get_advance_pay");
        Route::post("get_pay_trip", "Admin\Ticket\TicketController@get_pay_trip");
        Route::get("get_reference/{id}", "Admin\Ticket\TicketController@get_reference");

    });

    //this route for ticket
    Route::group(['prefix' => 'rescan', 'as' => 'rescan.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Admin\Rescan\RescanController@index']);
        Route::get('list', "Admin\Rescan\RescanController@index");
        Route::get('export', "Admin\Rescan\RescanController@export");

        Route::post('authorize', "Admin\Rescan\RescanController@store");
        Route::post('delete', "Admin\Rescan\RescanController@delete");
    });

    //this route for ticket
    Route::group(['prefix' => 'credit', 'as' => 'credit.'], function () {

        Route::get('list', ['as' => 'index', 'uses' => 'Admin\CreditNote\CreditNoteController@index']);
        Route::post('list', "Admin\CreditNote\CreditNoteController@index");
        Route::get('export', 'Admin\CreditNote\CreditNoteController@export');
        Route::get('data', "Admin\CreditNote\CreditNoteController@data");
        Route::post("reprint", "Admin\CreditNote\CreditNoteController@RePrint");

        Route::get('create', "Admin\CreditNote\CreditNoteController@create");
        Route::post('get_reference_info', "Admin\CreditNote\CreditNoteController@get_reference_info");
        Route::post("save", "Admin\CreditNote\CreditNoteController@store");
        Route::get("test", "Admin\CreditNote\CreditNoteController@testing");
        Route::get("get_reference/{id}", "Admin\CreditNote\CreditNoteController@get_reference");
        Route::get("get_ticket_number", "Admin\CreditNote\CreditNoteController@get_ticket_number");
    });

    //this route for round trip
    Route::group(['prefix' => 'round', 'as' => 'round.'], function () {

        Route::get('list', ['as' => 'index', 'uses' => 'Admin\Round\RoundTripController@index']);
        Route::post('list', 'Admin\Round\RoundTripController@index');
        Route::post('edit', 'Admin\Round\RoundTripController@EditRoundTrip');
        Route::post('get_data_type', 'Admin\Round\RoundTripController@get_data_type');
        Route::get('round_list', 'Admin\Round\RoundTripController@round_list');
        Route::post('update', 'Admin\Round\RoundTripController@update');
        Route::get('get_fleet_in_round_trip', 'Admin\Round\RoundTripController@get_fleet_in_round_trip');

        Route::get('create', 'Admin\Round\RoundTripController@create');
        Route::post('create', 'Admin\Round\RoundTripController@create');

        Route::get('create_round', 'Admin\Round\RoundTripController@create_round');
        Route::get("get_detail/{id}", "Admin\Round\RoundTripController@get_detail");
        Route::get("ticket_info/{id}", "Admin\Round\RoundTripController@ticket_info");

        Route::post("save", "Admin\Round\RoundTripController@store");
        Route::post("save_empty", "Admin\Round\RoundTripController@SaveEmpty");
        Route::post("get_add", "Admin\Round\RoundTripController@get_add");
        Route::post("data", "Admin\Round\RoundTripController@get_data");
        Route::get('get_ticket', 'Admin\Round\RoundTripController@getTicketNumber');

        Route::get('get_ticket_no_return', 'Admin\Round\RoundTripController@getTicketNumberNoReturn');
        Route::get('get_fleet_num_roundtrip', 'Admin\Round\RoundTripController@getFleetNumberNoReturn');
        Route::get('export_list', 'Admin\Round\RoundTripController@ExportList');
    });

    //this route for write off process
    Route::group(['prefix' => 'writeoff', 'as' => 'writeoff.'], function () {
        Route::get('list', ['as' => 'index', 'uses' => 'Admin\WriteOff\WriteOffController@request_list']);
        Route::post("list", "Admin\WriteOff\WriteOffController@request_list");
        Route::get("export", "Admin\WriteOff\WriteOffController@export_request");
        Route::get("data", "Admin\WriteOff\WriteOffController@data_request");
        Route::post("update", "Admin\WriteOff\WriteOffController@update");
        Route::post("reprint", "Admin\WriteOff\WriteOffController@RePrint");

        Route::get("create", "Admin\WriteOff\WriteOffController@create_request");
        Route::post("save", "Admin\WriteOff\WriteOffController@request_store");
        Route::post("get_ticket_number", "Admin\WriteOff\WriteOffController@get_ticket_number");

        Route::get("approve", "Admin\WriteOff\WriteOffController@approve_list");
        Route::post("approve", "Admin\WriteOff\WriteOffController@approve_list");
        Route::get("do_approve/{id}", "Admin\WriteOff\WriteOffController@do_approve");
        Route::get("export_approve", "Admin\WriteOff\WriteOffController@export_approve");
        Route::get("data_approve", "Admin\WriteOff\WriteOffController@data_approve");
        Route::post("approve_multiple", "Admin\WriteOff\WriteOffController@approve_multiple");
        Route::get("get_fleet_in_ticket", "Admin\WriteOff\WriteOffController@get_fleet_in_ticket");
        Route::post("get_ticket_number_filter", "Admin\WriteOff\WriteOffController@get_ticket_number_filter");
    });

    //this route group for report
    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {

        //ticket already scan
        Route::get('/', ['as' => 'index', 'uses' => 'Admin\Report\ReportController@index']);
        Route::get('sale', 'Admin\Report\ReportController@sale');
        Route::post('sale', 'Admin\Report\ReportController@sale');
        Route::get('get_fleet_in_sale', 'Admin\Report\ReportController@get_fleet_in_sale');
        Route::get('get_driver_in_sale', 'Admin\Report\ReportController@get_driver_in_sale');
        Route::get('get_number_sale', 'Admin\Report\ReportController@get_number_sale');
        Route::get('get_ticket_in_sale', 'Admin\Report\ReportController@get_ticket_in_sale');
        Route::get('export_sale', 'Admin\Report\ReportController@export_excel');

        //report all ticket data
        Route::get('ticket', 'Admin\Report\ReportController@get_ticket');
        Route::post('ticket', 'Admin\Report\ReportController@get_ticket');
        Route::get('export_ticket', 'Admin\Report\ReportController@export_ticket');
        Route::get('get_fleet_in_ticket', 'Admin\Report\ReportController@get_fleet_in_ticket');
        Route::get('get_driver_in_ticket', 'Admin\Report\ReportController@get_driver_in_ticket');
        Route::get('get_reason_in_ticket', 'Admin\Report\ReportController@get_reason_in_ticket');
        Route::get('get_destination_in_ticket', 'Admin\Report\ReportController@get_destination_in_ticket');
        Route::get('get_ticket_number', 'Admin\Report\ReportController@get_ticket_number');
        Route::get("get_reference_number", "Admin\Report\ReportController@get_reference_number");

        //report diesel return data
        Route::get('diesel', 'Admin\Report\ReportController@get_diesel');
        Route::post('diesel', 'Admin\Report\ReportController@get_diesel');
        Route::get('export_diesel', 'Admin\Report\ReportController@export_diesel');
        Route::get('get_fleet_in_credit', 'Admin\Report\ReportController@get_fleet_in_credit');
        Route::get('get_driver_in_credit', 'Admin\Report\ReportController@get_driver_in_credit');
        Route::get('get_ticket_in_credit', 'Admin\Report\ReportController@get_ticket_in_credit');
        Route::get('get_credit_number', 'Admin\Report\ReportController@get_credit_number');

        //report write off data
        Route::get('writeoff', 'Admin\Report\ReportController@get_writeoff');
        Route::post('writeoff', 'Admin\Report\ReportController@get_writeoff');
        Route::get('export_writeoff', 'Admin\Report\ReportController@export_writeoff');
        Route::get('get_fleet_in_writeoff', 'Admin\Report\ReportController@get_fleet_in_writeoff');
        Route::get('get_driver_in_writeoff', 'Admin\Report\ReportController@get_driver_in_writeoff');
        Route::get('get_ticket_in_writeoff', 'Admin\Report\ReportController@get_ticket_in_writeoff');
        Route::get('get_writeoff_number', 'Admin\Report\ReportController@get_writeoff_number');

        //this function for tank delivery
        Route::get('tank', 'Admin\Report\ReportController@tank_delivery');
        Route::post('tank', 'Admin\Report\ReportController@tank_delivery');
        Route::get('export_tank', 'Admin\Report\ReportController@export_tank');

        //this function for reconciliation stock
        Route::get('reconciliation', 'Admin\Report\ReportController@reconciliation');
        Route::post('reconciliation', 'Admin\Report\ReportController@reconciliation');
        Route::get('export_reconciliation', 'Admin\Report\ReportController@export_reconciliation');

        Route::get("get_team_leader", "Admin\Report\ReportController@get_team_leader");

        //this function for account balance
        Route::get('account', 'Admin\Report\ReportController@account');
        Route::post('account', 'Admin\Report\ReportController@account');

        Route::get('get_ticket_intopup', 'Admin\Report\ReportController@get_ticket_intopup');
        Route::get('get_fleet_intopup', 'Admin\Report\ReportController@get_fleet_intopup');

    });

    //this route group for request
    Route::group(['prefix' => 'request', 'as' => 'request.'], function () {

        //this blog for list request
        Route::get('/', ['as' => 'index', 'uses' => 'Admin\Request\ReportRequestControllerController@index']);
        Route::get('list', 'Admin\Request\RequestController@index');
        Route::post('list', 'Admin\Request\RequestController@index');
        Route::get('data', 'Admin\Request\RequestController@data');

        //this blog for create request
        Route::get('create', 'Admin\Request\RequestController@create');

        //this blog get plate number
        Route::get("get_fleet", "Admin\Request\RequestController@GetFleet");
        Route::get("get_team/{id}", "Admin\Request\RequestController@GetTeamByFleetID");

        //this blog to get trailer number
        Route::get("get_trailer", "Admin\Request\RequestController@GetTrailer");

        //this blog to get customer
        Route::get("get_customer", "Admin\Request\RequestController@GetCustomer");

        //this blog to get container
        Route::get("get_container", "Admin\Request\RequestController@GetContainer");

        //this blog to get container
        Route::post("clone_data", "Admin\Request\RequestController@CloneData");

        //this blog for autocomplete of note
        Route::post('autocomplete', 'Admin\Request\RequestController@Autocomplete');

        //this blog to save data
        Route::post("save", "Admin\Request\RequestController@store");

        //this blog to save data
        Route::post("delete", "Admin\Request\RequestController@delete");

        //this blog to create ticket
        Route::post("create_ticket", "Admin\Request\RequestController@create_ticket");

        //this blog to show data detail of request
        Route::get("show/{id}", "Admin\Request\RequestController@show");

        //this blog to show data update of request
        Route::get("edit/{id}", "Admin\Request\RequestController@edit");

        //this blog to edit data update of request
        Route::post("update", "Admin\Request\RequestController@update");

        //this blog to edit status of request
        Route::post("update_status", "Admin\Request\RequestController@update_status");

        //this blog to get fleet in request
        Route::get("get_fleet_in_request", "Admin\Request\RequestController@get_fleet_in_request");

        //this blog to get driver in request
        Route::get("get_driver_in_request", "Admin\Request\RequestController@get_driver_in_request");

        //this blog to get trailer in request
        Route::get("get_trailer_in_request", "Admin\Request\RequestController@get_trailer_in_request");

        //this blog to get reason in request
        Route::get("get_reason_in_request", "Admin\Request\RequestController@get_reason_in_request");

        //this blog to get purpose in request
        Route::get("get_purpose_in_request", "Admin\Request\RequestController@get_purpose_in_request");

        //this blog to get request in request
        Route::get("get_request_number", "Admin\Request\RequestController@get_request_number");

        //this blog to get supervisor in request
        Route::get("get_supervisor", "Admin\Request\RequestController@get_supervisor");

        //this blog to get reference number
        Route::get("get_reference_number", "Admin\Request\RequestController@get_reference_number");

        //this blog to get supervisor in request
        Route::get("get_supervisor_id", "Admin\Request\RequestController@get_supervisor_id");

        //this blog to get excel from request
        Route::get("export", "Admin\Request\RequestController@export");
    });
});
