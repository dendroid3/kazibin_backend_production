<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'loginUser']) -> name('user.login');
Route::post('/register', [App\Http\Controllers\Api\Auth\RegisterController::class, 'create']) -> name('user.create');
Route::post('/verify_email', [App\Http\Controllers\Api\Auth\RegisterController::class, 'verifyEmailAccount']) -> name('email.verify');

Route::middleware(['auth:api']) -> group(function(){
    Route::post('/create_profile', [App\Http\Controllers\Api\Auth\RegisterController::class, 'createProfile']) -> name('profile.create');
    Route::get('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logoutUser']) -> name('user.logout');
    Route::get('/getLogs', [App\Http\Controllers\Api\Log\LogsController::class, 'getLogMessages']) -> name('logs.get');

    //this group is for urls to add a task
    Route::group(['prefix' => 'create_task', 'middleware' => ['OwnershipCheck']], function(){
        Route::post('/step_1', [App\Http\Controllers\Api\Task\AdditionController::class, 'index']) -> name('task.create')->withoutMiddleware(['OwnershipCheck']);
        Route::post('/step_2', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepTwo']) -> name('task.step_2');
        Route::post('/step_3', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepThree']) -> name('task.step_3');
        Route::post('/step_4', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepFour']) -> name('task.step_4');
        Route::post('/step_5', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepFive']) -> name('task.step_5');
        Route::post('/step_6', [App\Http\Controllers\Api\Task\AdditionController::class, 'stepSix']) -> name('task.step_6');
    });

    /*
    this group is for urls to intereact with offers. Offers are when a writer creates a task an offers it to one or several of his writers. Such tasks will not be on display 
    on the public tasks page, neither will the be forwarded to the telegram channel
    */ 
    Route::group(['prefix' => 'offer', 'middleware' => ['OfferOwnershipCheck']], function(){
        Route::post('/accept', [App\Http\Controllers\Api\Offer\MainController::class, 'accept']) -> name('offer.accept');
        Route::post('/reject', [App\Http\Controllers\Api\Offer\MainController::class, 'reject']) -> name('offer.reject');
        Route::post('/pull', [App\Http\Controllers\Api\Offer\MainController::class, 'pull']) -> name('offer.pull') 
                -> middleware(['OwnershipCheck'])->withoutMiddleware(['OfferOwnershipCheck']);

    });

    /*
    this group is for urls to intereact with tasks they are already associated with either as a writer or as a broker. It also houses the tasks that are available to the public.
    */ 

    Route::group(['prefix' => 'task'], function(){
        Route::get('/get_all_posted_by_me', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllPostedByMe']) -> name('task.get_all_posted_by_me');
        Route::get('/get_all_done_by_me', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllDoneByMe']) -> name('task.get_all_done_by_me');
        Route::post('/get_available_for_bidding', [App\Http\Controllers\Api\Task\FetchController::class, 'getAllAvailableForBidding']) -> name('task.get_all_available_for_bidding')
            ->withoutMiddleware(['auth:api']);
        Route::get('/get_availability_details', [App\Http\Controllers\Api\Task\FetchController::class, 'getAvailabilityDetails']) -> name('task.get_availability_details')
            ->withoutMiddleware(['auth:api']);
    });

    /*
    this group is for urls that may be used in the process of acquiring and interacting with liaisons. Liaisons are either writers or brokers.
    */ 
    Route::group(['prefix' => 'liaison'], function(){
        Route::post('/get_all_writers', [App\Http\Controllers\Api\Liaison\WritersController::class, 'getAll']) -> name('writers.get_all');
        Route::post('/request/writer', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'sendRequestToWriter']) -> name('writer.request');
        Route::get('/requests/from_writers', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'getLiaisonRequestsFromWriters']) -> name('request.from_writers');
        Route::get('/requests/to_writers', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'getLiaisonRequestsToWriters']) -> name('request.to_writers');
        Route::get('/requests/from_brokers', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'getLiaisonRequestsFromBrokers']) -> name('request.to_brokers');
        Route::get('/requests/to_brokers', [App\Http\Controllers\Api\Liaison\LiaisonRequestController::class, 'getLiaisonRequestsToBrokers']) -> name('request.from_brokers');
    });

});


