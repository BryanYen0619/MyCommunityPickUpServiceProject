<?php

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

// *******************************
// 前台 API
// http://{donaim}/community/{community_id}/user/{user_id}
// *******************************
// 網址前綴需帶入 community_id 及 user_id
Route::group([
    'prefix' => 'community/{community_id}/user/{user_id}/freight',
    'as' => 'api.'
], function () {
    // *** 物流單列表 ***
    Route::get('orderFrom', 'api\TrackingPickUpReceiptController@index');
    // *** 新增物流單 ***
    Route::post('orderFrom', 'api\TrackingPickUpReceiptController@store');
    // *** 包裹狀態列表 ***
    Route::get('packageStatus', 'api\PackageInfoController@index');
    // *** 查詢包裹狀態 ***
    Route::get('packageStatus/{order_from_id}', 'api\PackageInfoController@getPackageInfoByTrackingId')->where('order_from_id','[0-9]+');
    // *** 更新包裹狀態 ***
    Route::post('refreshPackageStatus', 'api\PackageInfoController@refreshPackageInfo');
});