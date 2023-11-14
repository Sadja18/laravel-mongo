<?php

use App\Models\CustomerMongoDB;
use App\Models\CustomerSQL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/ping', function (Request $request) {
    $connection = DB::connection('mongodb');
    $msg = 'MongoDB is accessible!';
    try {
        $connection->command(['ping' => 1]);
    } catch (\Exception $e) {
        $msg = 'MongoDB is not accessible. Error: ' . $e->getMessage();
    }
    return ['msg' => $msg];
});

Route::get('/create_eloquent_sql/', function (Request $request) {
    $success = CustomerSQL::create([
        'guid' => 'cust_0000',
        'first_name' => 'John',
        'family_name' => 'Doe',
        'email' => 'j.doe@gmail.com',
        'address' => '123 my street, my city, zip, state, country'
    ]);
    return ['success' => $success];

    // ...
});

Route::get('/create_eloquent_mongo/', function (Request $request) {

    $customer = CustomerMongoDB::where('guid', 'cust_1111')->get();

    if (!empty($customer)) {
        return ['msg' => 'customer already exists'];
    } else {
        $success = CustomerMongoDB::create([
            'guid' => 'cust_1111',
            'first_name' => 'John',
            'family_name' => 'Doe',
            'email' => 'j.doe@gmail.com',
            'address' => '123 my street, my city, zip, state, country'
        ]);
        return ['success' => $success];

    }


    // ...
});

Route::get('/find_eloquent/', function (Request $request) {
    $customer = CustomerMongoDB::where('guid', 'cust_1111')->get();

    return ['customer' => $customer];
    // ...
});

Route::get('/update_eloquent/', function (Request $request) {
    $result = CustomerMongoDB::where('guid', 'cust_1111')->update(['first_name' => 'Jimmy']);
    // ...
    return ['result' => $result];
});

Route::get('/delete_eloquent/', function (Request $request) {
    $result = CustomerMongoDB::where('guid', 'cust_1111')->delete();
    // ...
    return ['result' => $result];

});

Route::get('/create_nested/', function (Request $request) {
    $message = "executed";
    $success = null;

    $address = new stdClass;
    $address->street = '123 my street name';
    $address->city = 'my city';
    $address->zip = '12345';
    $emails = ['j.doe@gmail.com', 'j.doe@work.com'];

    try {
        $customer = new CustomerMongoDB();
        $customer->guid = 'cust_2222';
        $customer->first_name = 'John';
        $customer->family_name = 'Doe';
        $customer->email = $emails;
        $customer->address = $address;
        $success = $customer->save(); // save() returns 1 or 0
    } catch (\Exception $e) {
        $message = $e->getMessage();
    }
    return ['msg' => $message, 'data' => $success];
});