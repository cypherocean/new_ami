<?php

use Illuminate\Support\Facades\Route;

Route::get('command/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "config, cache, and view cleared successfully";
});

Route::get('command/config', function() {
    Artisan::call('config:cache');
    return "config cache successfully";
});

Route::get('command/key', function() {
    Artisan::call('key:generate');
    return "Key generate successfully";
});

Route::get('command/migrate', function() {
    Artisan::call('migrate:refresh');
    return "Database migration generated";
});

Route::get('seed', function() {
    Artisan::call('db:seed');
    return "Database seeding generated";
});

Route::group(['middleware' => ['prevent-back-history']], function(){
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', 'AuthController@login')->name('login');
        Route::post('signin', 'AuthController@signin')->name('signin');

        Route::get('forget-password', 'AuthController@forget_password')->name('forget.password');
        Route::post('password-forget', 'AuthController@password_forget')->name('password.forget');
        Route::get('reset-password/{string}', 'AuthController@reset_password')->name('reset.password');
        Route::post('recover-password', 'AuthController@recover_password')->name('recover.password');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');

        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

        /** users */
            Route::any('users', 'UsersController@index')->name('users');
            Route::get('users/create', 'UsersController@create')->name('users.create');
            Route::post('users/insert', 'UsersController@insert')->name('users.insert');
            Route::get('users/view/{id?}', 'UsersController@view')->name('users.view');
            Route::get('users/edit/{id?}', 'UsersController@edit')->name('users.edit');
            Route::patch('users/update', 'UsersController@update')->name('users.update');
            Route::post('users/change-status', 'UsersController@change_status')->name('users.change.status');
        /** users */

        /** products */
            Route::any('products', 'ProductsController@index')->name('products');
            Route::get('products/create', 'ProductsController@create')->name('products.create');
            Route::post('products/insert', 'ProductsController@insert')->name('products.insert');
            Route::get('products/view/{id?}', 'ProductsController@view')->name('products.view');
            Route::get('products/edit/{id?}', 'ProductsController@edit')->name('products.edit');
            Route::patch('products/update', 'ProductsController@update')->name('products.update');
            Route::get('products/delete/{id?}', 'ProductsController@delete')->name('products.delete');

            Route::post('products/insert-ajax', 'ProductsController@insert_ajax')->name('products.insert.ajax');
        /** products */

        /** strips */
            Route::any('strips', 'StripsController@index')->name('strips');
            Route::get('strips/create', 'StripsController@create')->name('strips.create');
            Route::post('strips/insert', 'StripsController@insert')->name('strips.insert');
            Route::get('strips/view/{id?}', 'StripsController@view')->name('strips.view');
            Route::get('strips/edit/{id?}', 'StripsController@edit')->name('strips.edit');
            Route::patch('strips/update', 'StripsController@update')->name('strips.update');
            Route::get('strips/delete/{id?}', 'StripsController@delete')->name('strips.delete');
        /** strips */

        /** customers */
            Route::any('customers', 'CustomerController@index')->name('customers');
            Route::get('customers/create', 'CustomerController@create')->name('customers.create');
            Route::post('customers/insert', 'CustomerController@insert')->name('customers.insert');
            Route::get('customers/view/{id?}', 'CustomerController@view')->name('customers.view');
            Route::get('customers/edit/{id?}', 'CustomerController@edit')->name('customers.edit');
            Route::patch('customers/update', 'CustomerController@update')->name('customers.update');
            Route::post('customers/change-status', 'CustomerController@change_status')->name('customers.change.status');

            Route::post('customers/insert-ajax', 'CustomerController@insert_ajax')->name('customers.insert.ajax');
        /** customers */

        /** notice */
            Route::any('notices', 'NoticesController@index')->name('notices');
            Route::get('notices/create', 'NoticesController@create')->name('notices.create');
            Route::post('notices/insert', 'NoticesController@insert')->name('notices.insert');
            Route::get('notices/edit/{id?}', 'NoticesController@edit')->name('notices.edit');
            Route::patch('notices/update', 'NoticesController@update')->name('notices.update');
            Route::post('notices/change-status', 'NoticesController@change_status')->name('notices.change.status');
        /** notice */

        /** notice-board */
            Route::get('notice-board', 'NoticesController@notices_board')->name('notice.board');
        /** notice-board */ 

        /** tasks */
            Route::any('tasks', 'TasksController@index')->name('tasks');
            Route::get('tasks/create', 'TasksController@create')->name('tasks.create');
            Route::post('tasks/insert', 'TasksController@insert')->name('tasks.insert');
            Route::get('tasks/view/{id?}', 'TasksController@view')->name('tasks.view');
            Route::get('tasks/edit/{id?}', 'TasksController@edit')->name('tasks.edit');
            Route::patch('tasks/update', 'TasksController@update')->name('tasks.update');
            Route::post('tasks/change-status', 'TasksController@change_status')->name('tasks.change.status');

            Route::post('tasks/customer_details', 'TasksController@customer_details')->name('tasks.customer.details');
        /** tasks */

        /** my-tasks */
            Route::any('mytasks', 'MyTasksController@index')->name('mytasks');
            Route::get('mytasks/view/{id?}', 'MyTasksController@view')->name('mytasks.view');
            Route::post('mytasks/change-status', 'MyTasksController@change_status')->name('mytasks.change.status');
        /** my-tasks */

        /** orders */
            Route::any('orders', 'OrdersController@index')->name('orders');
            Route::get('orders/create/{customer_id?}', 'OrdersController@create')->name('orders.create');
            Route::post('orders/insert', 'OrdersController@insert')->name('orders.insert');
            Route::get('orders/view/{id?}', 'OrdersController@view')->name('orders.view');
            Route::get('orders/edit/{id?}', 'OrdersController@edit')->name('orders.edit');
            Route::patch('orders/update', 'OrdersController@update')->name('orders.update');
            Route::post('orders/change-status', 'OrdersController@change_status')->name('orders.change.status');

            Route::post('orders/delete-detail', 'OrdersController@delete_detail')->name('orders.delete.detail');
            Route::post('orders/delete-strip', 'OrdersController@delete_strip')->name('orders.delete.strip');
            Route::get('orders/select-customer', 'OrdersController@select_customer')->name('orders.select.customer');

            Route::post('orders/customer-details', 'OrdersController@customer_details')->name('orders.customer.details');
            Route::post('orders/product-price', 'OrdersController@product_price')->name('orders.product.price');
            Route::post('orders/product-strip', 'OrdersController@strip_price')->name('orders.strip.price');
            Route::post('orders/product-amp', 'OrdersController@strip_amp')->name('orders.strip.amp');
        /** orders */

        /** purchase-orders */
            Route::any('purchase_orders', 'PurchaseOrderController@index')->name('purchase_orders');
            Route::get('purchase_orders/create/{customer_id?}', 'PurchaseOrderController@create')->name('purchase_orders.create');
            Route::post('purchase_orders/insert', 'PurchaseOrderController@insert')->name('purchase_orders.insert');
            Route::get('purchase_orders/view/{id?}', 'PurchaseOrderController@view')->name('purchase_orders.view');
            Route::get('purchase_orders/edit/{id?}', 'PurchaseOrderController@edit')->name('purchase_orders.edit');
            Route::patch('purchase_orders/update', 'PurchaseOrderController@update')->name('purchase_orders.update');
            Route::post('purchase_orders/change-status', 'PurchaseOrderController@change_status')->name('purchase_orders.change.status');

            Route::post('purchase_orders/delete-detail', 'PurchaseOrderController@delete_detail')->name('purchase_orders.delete.detail');
            Route::post('purchase_orders/product-detail', 'PurchaseOrderController@product_detail')->name('purchase_orders.product.detail');
        /** purchase-orders */

        /** payment */
            Route::any('payments', 'PaymentController@index')->name('payment');
            Route::get('payment/import', 'PaymentController@file_import')->name('payment.import.file');
            Route::post('payment/import', 'PaymentController@import')->name('payment.import');
            Route::post('payment/assign', 'PaymentController@assign')->name('payment.assign');
            Route::get('payment/assigned-users', 'PaymentController@assigned_users')->name('payment.assigned.users');
            Route::post('payment/info-model', 'PaymentController@infoModel')->name('payment.info.model');
            Route::post('payment/assign-model', 'PaymentController@assignModel')->name('payment.assign.model');
            /** payment */
            
            /** payments-reminder */
            Route::any('payments-reminder', 'PaymentReminderController@index')->name('payments.reminders');
            Route::post('payments-reminder/insert', 'PaymentReminderController@insert')->name('payments.reminders.insert');
            Route::post('payments-reminder/change-status', 'PaymentReminderController@change_status')->name('payments.reminders.change.status');
            Route::get('payments-reminder/reports', 'PaymentReminderController@reports')->name('payments.reminders.reports');
            Route::post('payment/add-followup', 'PaymentReminderController@addFollowup')->name('payment.add.followup');
            Route::post('payment/followup-details', 'PaymentReminderController@followupDetails')->name('payment.followup.details');
            Route::post('payment/bill-details', 'PaymentReminderController@billDetails')->name('payment.bill.details');
        /** payments-reminder */
        
        /** reminder */
            Route::any('reminders', 'ReminderController@index')->name('reminders');
            Route::get('reminders/create', 'ReminderController@create')->name('reminders.create');
            Route::post('reminders/insert', 'ReminderController@insert')->name('reminders.insert');
            Route::get('reminders/view/{id?}', 'ReminderController@view')->name('reminders.view');
            Route::get('reminders/edit/{id?}', 'ReminderController@edit')->name('reminders.edit');
            Route::patch('reminders/update', 'ReminderController@update')->name('reminders.update');
            Route::post('reminders/change-status', 'ReminderController@change_status')->name('reminders.change.status');
        /** reminder */

        /** Pre Defined Message */
            Route::any('pre_defined_message', 'PreDefinedMessageController@index')->name('pre_defined_message');
            Route::get('pre_defined_message/create', 'PreDefinedMessageController@create')->name('pre_defined_message.create');
            Route::post('pre_defined_message/insert', 'PreDefinedMessageController@insert')->name('pre_defined_message.insert');
            Route::get('pre_defined_message/view/{id?}', 'PreDefinedMessageController@view')->name('pre_defined_message.view');
            Route::get('pre_defined_message/edit/{id?}', 'PreDefinedMessageController@edit')->name('pre_defined_message.edit');
            Route::patch('pre_defined_message/update', 'PreDefinedMessageController@update')->name('pre_defined_message.update');
            Route::post('pre_defined_message/change-status', 'PreDefinedMessageController@change_status')->name('pre_defined_message.change.status');
        /** Pre Defined Message */

        /** Calendar */
        Route::controller(CalenderController::class)
            ->prefix('calendar')
            ->as('calendar.')
            ->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::POST('/insert', 'insert')->name('insert');
                Route::get('/view/{id?}', 'view')->name('view');
                Route::get('/edit/{id?}', 'edit')->name('edit');
                Route::post('/update', 'update')->name('update');
                Route::post('/change-status', 'change_status')->name('change.status');
                Route::post('/fetch-events', 'fetchEvents')->name('fetch.events');
            });
        /** Calendar */
    });

    Route::get("{path}", function(){ return redirect()->route('login'); })->where('path', '.+');
});