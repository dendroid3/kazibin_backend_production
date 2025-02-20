<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/download', [App\Http\Controllers\Api\Download\DownloadController::class, 'download']) -> name('file.download');

Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'loginUser']) -> name('user.login');
Route::post('/register', [App\Http\Controllers\Api\Auth\RegisterController::class, 'create']) -> name('user.create');
Route::post('/verify_email', [App\Http\Controllers\Api\Auth\RegisterController::class, 'verifyEmail']) -> name('email.verify');
Route::post('/initialise_password_reset', [App\Http\Controllers\Api\Auth\RegisterController::class, 'initialisePasswordReset']) -> name('password.initialise_reset');
Route::post('/reset_password', [App\Http\Controllers\Api\Auth\RegisterController::class, 'resetPassword']) -> name('password.reset');
Route::post('/log_error', [App\Http\Controllers\Api\Error\ErrorController::class, 'logError']) -> name('error.log');
Route::get('/get_about_statistics', [App\Http\Controllers\Api\Statistics\StatisticsController::class, 'getAboutStatistics']) -> name('statistics.get');
Route::post('/get_one_broker', [App\Http\Controllers\Api\Liaison\BrokersController::class, 'getOneBroker']) -> name('brokers.get_one');
Route::get('/get_available_for_bidding_landing', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllAvailableForBiddingLanding']) -> name('task.get_all_available_for_bidding_landing');
Route::post('/get_total_available_tasks', [App\Http\Controllers\Api\Task\FetchController::class, 'getTotalAvailableTasks']) -> name('task.get_total_available_tasks');


Route::post('/trans', [App\Http\Controllers\Api\Profile\ProfileController::class, 'trans']) -> name('profidle.change_my_bio');


Route::middleware(['auth:api', 'RecordLastActivity']) -> group(function(){
    Route::get('/resend_verification_email', [App\Http\Controllers\Api\Auth\RegisterController::class, 'resendVerificationEmail']) -> name('profile.create');
    Route::get('/is_account_verified', [App\Http\Controllers\Api\Auth\RegisterController::class, 'isAccountVerified']) -> name('profile.create');
    Route::post('/create_profile', [App\Http\Controllers\Api\Auth\RegisterController::class, 'createProfile']) -> name('profile.create');
    Route::get('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logoutUser']) -> name('user.logout');
    Route::get('/getLogs', [App\Http\Controllers\Api\Log\LogsController::class, 'getLogMessages']) -> name('logs.get');

    Route::post('/initialise_verification', [App\Http\Controllers\Api\Verfication\VerificationsController::class, 'initiateVerification']) 
    -> middleware('canInitiateVerification')
    -> name('verification.initialise');

    Route::group(['prefix' => 'create_task', 'middleware' => ['OwnershipCheck']], function(){
        Route::post('/step_1', [App\Http\Controllers\Api\Task\AdditionController::class, 'index']) -> name('task.create')->withoutMiddleware(['OwnershipCheck']);
        Route::post('/step_2', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepTwo']) -> name('task.step_2');
        Route::post('/step_3', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepThree']) -> name('task.step_3');
        Route::post('/step_4', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepFour']) -> name('task.step_4');
        Route::post('/step_5', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepFive']) -> name('task.step_5');
        Route::post('/step_6', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepSix']) -> name('task.step_6');

        Route::post('/change_deadline', [App\Http\Controllers\Api\Task\AdditionController::class, 'changeDeadline']) -> name('task.changeDeadline');
        Route::post('/change_payment', [App\Http\Controllers\Api\Task\AdditionController::class, 'changePayment']) -> name('task.changePayment');
    });

    Route::group(['prefix' => 'search'], function(){
        Route::post('/from_home', [App\Http\Controllers\Api\Search\SearchController::class, 'searchFromHome']) -> name('search.from_home');
    });

    Route::group(['prefix' => 'offer'], function(){
        Route::post('/pull', [App\Http\Controllers\Api\Offer\MainController::class, 'pull']) -> name('offer.pull');
        Route::get('/get_mine', [App\Http\Controllers\Api\Offer\MainController::class, 'getMine']) -> name('offer.get_mine');
        Route::post('/get_mine_paginated', [App\Http\Controllers\Api\Offer\MainController::class, 'getMinePaginated']) -> name('offer.get_mine_paginated');
        Route::post('/get_messages', [App\Http\Controllers\Api\Offer\MainController::class, 'getOfferMessages']) -> name('offer.accept');
        Route::post('/send_message', [App\Http\Controllers\Api\Offer\MainController::class, 'sendOfferMessage']) -> name('offer.send_message');

        Route::group(['middleware' => ['OfferOwnershipCheck']], function(){
            Route::post('/accept', [App\Http\Controllers\Api\Offer\MainController::class, 'accept']) -> name('offer.accept');
            Route::post('/reject', [App\Http\Controllers\Api\Offer\MainController::class, 'reject']) -> name('offer.reject');
            Route::post('/cancel', [App\Http\Controllers\Api\Offer\MainController::class, 'cancel']) -> name('offer.cancel') -> withoutMiddleware(['OfferOwnershipCheck']);
        });
    });

    Route::group(['prefix' => 'task'], function(){
        Route::get('/get_all_posted_by_me', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllPostedByMe']) -> name('task.get_all_posted_by_me');
        Route::post('/get_all_posted_by_me_paginated', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllPostedByMePaginated']) -> name('task.get_all_posted_by_me');
        Route::get('/get_all_done_by_me', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllDoneByMe']) -> name('task.get_all_done_by_me');
        Route::post('/get_all_done_by_me_paginated', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllDoneByMePaginated']) -> name('task.get_all_done_by_me_paginated');

        Route::post('/get_available_for_bidding', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllAvailableForBidding']) -> name('task.get_all_available_for_bidding');
        Route::post('/get_available_for_bidding_paginated', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllAvailableForBiddingPaginated']) -> name('task.get_all_available_for_bidding');
        
        Route::get('/get_availability_details', [App\Http\Controllers\Api\Task\FetchController::class, 'getAvailabilityDetails']) -> name('task.get_availability_details')
            ->withoutMiddleware(['auth:api', 'RecordLastActivity']);
        Route::post('/get_for_bidding', [App\Http\Controllers\Api\Task\FetchController::class, 'getTaskForBidding']) -> name('task.get_for_bidding')
            ->withoutMiddleware(['auth:api', 'RecordLastActivity']);
        
        Route::post('/get_messages', [App\Http\Controllers\Api\Task\MessageController::class, 'getMessages']) -> name('task.get_messages');
        Route::post('/send_message', [App\Http\Controllers\Api\Task\MessageController::class, 'sendMessage']) -> name('task.send_message');
        Route::post('/mark_complete', [App\Http\Controllers\Api\Task\ChangeStatusController::class, 'markComplete']) -> name('task.mark_complete');
        Route::post('/rate_user', [App\Http\Controllers\Api\Rating\RatingsController::class, 'Rate']) -> name('user.rate');
        Route::post('/delete', [App\Http\Controllers\Api\Task\AdditionController::class, 'deleteTask']) -> middleware('OwnershipCheck') -> name('task.delete');

    });

    Route::group(['prefix' => 'profile'], function(){
        Route::get('/get_dashboard_details', [App\Http\Controllers\Api\Profile\ProfileController::class, 'getDashboardDetails']) -> name('profile.get_dashboard_details');
        Route::post('/get_broker_metrics', [App\Http\Controllers\Api\Profile\ProfileController::class, 'getBrokerMetrics']) -> name('broker.get_broker_metrics');
        Route::post('/get_writer_metrics', [App\Http\Controllers\Api\Profile\ProfileController::class, 'getWriterMetrics']) -> name('writer.get_writer_metrics');
        Route::post('/set_my_interests', [App\Http\Controllers\Api\Profile\ProfileController::class, 'setMyInterests']) -> name('profile.set_my_interests');
        Route::post('/change_my_bio', [App\Http\Controllers\Api\Profile\ProfileController::class, 'changeMyBio']) -> name('profile.change_my_bio');
        Route::post('/change_availability', [App\Http\Controllers\Api\Profile\ProfileController::class, 'changeAvailability']) -> name('profile.change_availability');
    });

    Route::group(['prefix' => 'transaction'], function(){
        Route::get('/get_mine', [App\Http\Controllers\Api\Transaction\TransactionsController::class, 'getTransactions']) -> name('transactions.get');
        Route::get('/get_mine_paginated', [App\Http\Controllers\Api\Transaction\TransactionsController::class, 'getTransactionsPaginated']) -> name('transactions.get_paginated');
        Route::post('/claim', [App\Http\Controllers\Api\Transaction\TransactionsController::class, 'claimTransaction']) -> name('transaction.claim');
        Route::post('/deposit_from_mpesa', [App\Http\Controllers\Api\Transaction\TransactionsController::class, 'depositFromMpesa']) -> name('transaction.deposit');
        Route::post('/record_transaction', [App\Http\Controllers\Api\Transaction\TransactionsController::class, 'recordTransaction']) -> name('transaction.record')->withoutMiddleware(['auth:api', 'RecordLastActivity']);
        Route::post('/get_all_done_by_me_from_from_broker_for_creating_invoice', 
                [
                    App\Http\Controllers\Api\Task\FetchController::class, 
                    'getAllDoneByMeFromBrokerForCreatingInvoice'
                ]) 
                -> name('task.get_all_done_by_me_from_from_broker_for_creating_invoice');
        Route::post('/get_all_done_by_writer_for_creating_invoice', 
                [
                    App\Http\Controllers\Api\Task\FetchController::class, 
                    'getAllDoneByWriterForCreatingInvoice'
                ]) 
                -> name('task.get_all_done_by_writer_for_creating_invoice');
        Route::post('/create_invoice', [App\Http\Controllers\Api\Transaction\InvoiceController::class, 'createInvoice']) -> name('invoice.create');
        Route::post('/get_invoice', [App\Http\Controllers\Api\Transaction\InvoiceController::class, 'getInvoice']) -> name('invoice.get');
        Route::get('/get_invoices', [App\Http\Controllers\Api\Transaction\InvoiceController::class, 'getInvoices']) -> name('invoices.get');
        Route::post('/get_invoices_paginated', [App\Http\Controllers\Api\Transaction\InvoiceController::class, 'getInvoicesPaginated']) -> name('invoices.get_paginated');
        Route::post('/mark_paid', [App\Http\Controllers\Api\Transaction\InvoiceController::class, 'markPaid']) -> name('invoices.mark_paid');
        Route::post('/confirm_paid', [App\Http\Controllers\Api\Transaction\InvoiceController::class, 'confirmPaid']) -> name('invoices.confirm_paid');
        Route::get('/get_network_in_deficit', [App\Http\Controllers\Api\Transaction\InvoiceController::class, 'getNetworkInDeficit']) -> name('invoices.get_network_in_deficit');
    });

    Route::group(['prefix' => 'liaison'], function(){
        Route::post('/get_all_writers', [App\Http\Controllers\Api\Liaison\WritersController::class, 'getAll']) -> name('writers.get_all');
        Route::post('/get_all_writers_paginated', [App\Http\Controllers\Api\Liaison\WritersController::class, 'getAllPaginated']) -> name('writers.get_all_paginated');
        Route::post('/get_all_brokers', [App\Http\Controllers\Api\Liaison\BrokersController::class, 'getAll']) -> name('brokers.get_all');
        Route::post('/get_all_brokers_paginated', [App\Http\Controllers\Api\Liaison\BrokersController::class, 'getAllPaginated']) -> name('brokers.get_all_paginated');

        Route::post('/request/writer', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'sendRequestToWriter']) -> name('writer.request');
        Route::post('/request/broker', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'sendRequestToBroker']) -> name('broker.request');

        Route::post('/request/reject', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'rejectRequest']) -> name('request.reject');
        Route::post('/request/accept', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'acceptRequest']) -> name('request.accept');

        Route::get('/requests/get_all', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'getLiaisonRequests']) -> name('request.to_brokers');
        Route::post('/requests/get_all_paginated', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'getLiaisonRequestsPaginated']) -> name('request.get_all_paginated');

        Route::post('/request/send_message', [App\Http\Controllers\Api\Liaison\LiaisonMessagesController::class, 'sendRequestMessage']) -> name('request.send_message');
        Route::post('/request/get_messages', [App\Http\Controllers\Api\Liaison\LiaisonMessagesController::class, 'getRequestMessages']) -> name('request.messages');
        Route::post('/request/set_cost_per_page', [App\Http\Controllers\Api\Liaison\LiaisonMessagesController::class, 'setCostPerPage']) -> name('request.setCPP');

        Route::get('/get_my_writers', [App\Http\Controllers\Api\Liaison\WritersController::class, 'getMyWriters']) -> name('writers.get_mine');
        Route::post('/get_my_writer', [App\Http\Controllers\Api\Liaison\WritersController::class, 'getMyWriter']) -> name('writer.get_mine');
        Route::post('/get_my_writer_offers', [App\Http\Controllers\Api\Liaison\WritersController::class, 'getMyWriterOFfers']) -> name('writer.get_my_offers');
        Route::post('/get_my_writer_bids', [App\Http\Controllers\Api\Liaison\WritersController::class, 'getMyWriterBids']) -> name('writer.get_my_bids');
        Route::post('/get_my_writer_invoices', [App\Http\Controllers\Api\Liaison\WritersController::class, 'getMyWriterInvoices']) -> name('writer.get_my_invoices');

        Route::get('/get_my_brokers', [App\Http\Controllers\Api\Liaison\BrokersController::class, 'getMyBrokers']) -> name('brokers.get_mine');
        Route::post('/get_my_broker', [App\Http\Controllers\Api\Liaison\BrokersController::class, 'getMyBroker']) -> name('broker.get_mine');
        Route::post('/get_my_broker_offers', [App\Http\Controllers\Api\Liaison\BrokersController::class, 'getMyBrokerOFfers']) -> name('broker.get_my_offers');
        Route::post('/get_my_broker_bids', [App\Http\Controllers\Api\Liaison\BrokersController::class, 'getMyBrokerBids']) -> name('broker.get_my_bids');
        Route::post('/get_my_broker_invoices', [App\Http\Controllers\Api\Liaison\BrokersController::class, 'getMyBrokerInvoices']) -> name('broker.get_my_invoices');
    });

    Route::group(['prefix' => 'bid'], function(){
        Route::post('/create', [App\Http\Controllers\Api\Bid\MainController::class, 'create']) -> name('bid.create');
        Route::get('/get_mine', [App\Http\Controllers\Api\Bid\MainController::class, 'getMyBids']) -> name('bid.get_mine');
        Route::post('/get_mine_paginated', [App\Http\Controllers\Api\Bid\MainController::class, 'getMyBidsPaginated']) -> name('bid.get_mine_paginated');
        Route::post('/get_messages', [App\Http\Controllers\Api\Bid\MainController::class, 'getBidMessages']) -> name('bid.get_messages');
        Route::post('/send_message', [App\Http\Controllers\Api\Bid\MainController::class, 'sendBidMessage']) -> name('bid.send_message');

        Route::post('/accept', [App\Http\Controllers\Api\Bid\MainController::class, 'acceptBid']) -> name('bid.accept');
        Route::post('/reject', [App\Http\Controllers\Api\Bid\MainController::class, 'rejectBid']) -> name('bid.reject');
        Route::post('/pull', [App\Http\Controllers\Api\Bid\MainController::class, 'pullBid']) -> name('bid.pull');
    });

    Route::group(['prefix' => 'marketplace'], function(){
        Route::post('/create', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'create']) -> name('marketplace.create');
        Route::post('/update', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'update']) -> name('marketplace.update');
        Route::delete('/delete', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'delete']) -> name('marketplace.delete');
        Route::post('/get_current', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'getCurrentAccount']) -> name('marketplace.get_account') -> withoutMiddleware(['auth:api', 'RecordLastActivity']);


        Route::post('/get_mine', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'getMine']) -> name('marketplace.get_mine');
        Route::get('/get_for_display', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'getSomeForDisplay']) -> name('marketplace.get_for_display');
        Route::get('/get_paginated', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'getAllPaginated']) -> name('marketplace.get_paginated');

        Route::get('/get_for_display_guest', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'getSomeForDisplay']) -> name('marketplace.get_for_display') -> withoutMiddleware(['auth:api', 'RecordLastActivity']);
        Route::get('/get_paginated_guest', [App\Http\Controllers\Api\Marketplace\MarketplaceController::class, 'getAllPaginated']) -> name('marketplace.get_paginated') -> withoutMiddleware(['auth:api', 'RecordLastActivity']);
    });

    Route::group(['prefix' => 'managed'], function(){
        Route::post('/create', [App\Http\Controllers\Api\ManagedAccounts\ManagedAccountsController::class, 'create']) -> name('managed.create');
        Route::post('/get', [App\Http\Controllers\Api\ManagedAccounts\ManagedAccountsController::class, 'get']) -> name('managed.get');
    });

    Route::group(['prefix' => 'services'], function(){
        Route::get('/get', [App\Http\Controllers\Api\Service\ServicesController::class, 'get']) -> name('service.get') -> withoutMiddleware(['auth:api', 'RecordLastActivity']);
    });

    Route::group(['prefix' => 'admin'], function(){
        Route::post('/create', [App\Http\Controllers\Api\Admin\AdminController::class, 'create']) -> name('accounts.create');
    });
});
