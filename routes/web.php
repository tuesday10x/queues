<?php

use App\Thing;
use App\Jobs\TestJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

Route::get('/', function () {
    return view('welcome');
});


Auth::routes([
    'verify' => true
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');















Route::get('not_queued', function(){
    dump( now()->format('h:i:s A T') );
    sleep(5);
    dump( now()->format('h:i:s A T') );

    return;
});







Route::get('queued', function(){
    dispatch(function(){
        dump( now()->format('h:i:s A T') );
        sleep(5);
        dump( now()->format('h:i:s A T') );

        return;
    });

    // didn't work. what gives?
    // check env for driver
});







Route::get('queued1', function(){
    config(['queue.default' => 'database']);

    dispatch(function(){
        dump( now()->format('h:i:s A T') );
        sleep(5);
        dump( now()->format('h:i:s A T') );

        return;
    });

    // didn't work. what gives?
    // check env for driver
});

// How do we run the queue?
// php artisan queue:work
// php artisan queue:listen


// How do we add to the queue?
// Queue::push(new Thing()); //handle method
// dispatch(new Thing()); // use Dispatchable
// ClassName::dispatch(); // use Dispatchable
// Mail::to()->send(new Mailable()); // will queue automatically if implements ShouldQueue



// Anonymous Jobs
// dispatch(function(){
//      do some stuff here...
// });

Route::get('doit', function(){
    $batch = Bus::batch([
        new TestJob(1),
        new TestJob(5),
    ])->then(function (Batch $batch) {
        // All jobs completed successfully...
        Mail::raw('Hey Done', function($message){
            $message->to('ben@modernmcguire.com')->subject('Done');
        });

        // event(new DoSomeStuff($batch));
    })->catch(function (Batch $batch, Throwable $e) {
        // First batch job failure detected...
        Mail::raw('Shits Broke', function($message){
            $message->to('ben@modernmcguire.com')->subject('Done');
        });
    })->finally(function (Batch $batch) {
        // The batch has finished executing...

        return 'something here';
    })
    ->name('floofy')
    ->dispatch();

    return redirect('batch/' . $batch->id);

    return $batch;

    // return $var;
});

Route::get('batch/{batchId}', function($batchId){
    return Bus::findBatch($batchId);
});
