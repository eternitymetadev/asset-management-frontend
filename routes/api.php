<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\JwtAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\InventoryController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User: Register, Login
Route::post('login', [JwtAuthController::class,'authenticate']);
Route::get('logout', [UserController::class, 'logout']);

Route::group(['middleware' => ['jwt.verify']], function() {
    // Route::post('register', [JwtAuthController::class,'registerauthenticate']);  //create user
    Route::post('register', [UserController::class,'registerUser']);  //create user
    Route::get('users', [UserController::class,'userList']);
    Route::get('get-role', [UserController::class,'getRole']);
    Route::post('update-user/{id}', [UserController::class, 'updateUser']);
    Route::get('user-detail/{id}', [UserController::class, 'userDetail']);
    Route::post('delete-user/{id}', [UserController::class,'deleteUser']);

    Route::get('categories', [SettingController::class, 'categoryList']);
    Route::post('add-category', [SettingController::class, 'addCategory']);
    Route::post('update-category', [SettingController::class, 'updateCategory']);

    Route::get('brands', [SettingController::class, 'brandList']);
    Route::post('add-brand', [SettingController::class, 'addBrand']);
    Route::post('update-brand', [SettingController::class, 'updateBrand']);

    Route::get('inventories', [InventoryController::class, 'inventoryList']);
    Route::post('create-inventory', [InventoryController::class, 'createInventory']);
    Route::post('update-inventory', [InventoryController::class, 'updateInventory']);
    Route::post('update-assign-status', [InventoryController::class, 'updateAssignStatus']);
    Route::post('scrap-email-request', [InventoryController::class, 'scrapEmailRequest']);
    

    Route::get('get-vendors/{id}', [InventoryController::class, 'getVendor']);
    Route::get('get-brand', [InventoryController::class,'getBrand']);

    Route::any('inventory/export', [InventoryController::class, 'exportInventory']);
    Route::post('inventory/bulk-upload', [InventoryController::class, 'bulkUpload']);

    Route::post('undertaking-upload/{id}', [InventoryController::class, 'undertakingUpload']);
    Route::post('handover-employee', [InventoryController::class, 'handoverEmployee']);
    Route::post('pullback-employee', [InventoryController::class, 'pullbackToEmployee']);

    Route::any('accept-pullback', [InventoryController::class, 'acceptPullback']);

     
    // Route::any('/settings/delete-category', [SettingController::class, 'deleteCategory']);

});

Route::get('check-serialno/{sno}', [InventoryController::class,'CheckSerialno']);
// Route::get('get-employee', [InventoryController::class, 'getEmployee']);
// Route::get('get-employee-detail/{id}', [InventoryController::class, 'getEmployeeDetail']);
Route::any('accept-asset/{id}', [InventoryController::class, 'acceptAsset']);
Route::any('approved-asset/{id}', [InventoryController::class, 'approvedAsset']);
// Route::any('accept-pullback/{id}', [InventoryController::class, 'acceptPullback']);
Route::any('accept-scrap/{id}', [InventoryController::class, 'acceptScrap']);

Route::any('declined-asset/{id}', [InventoryController::class, 'declinedAsset']);
Route::get('pdf-inventory/{id}', [InventoryController::class, 'pdfInventory']);

Route::get('inventory/sample-inventories',[InventoryController::class, 'inventorySampleDownload']);

Route::get('/forgot-session', [DashboardController::class, 'ForgotSession']);