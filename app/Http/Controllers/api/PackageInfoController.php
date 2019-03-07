<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\AddPackageRequest;
use App\Http\Requests\KerryPackageInfoRequest;
use App\Repositories\PackageInfoRepository;
use App\Repositories\TrackingPickUpReceiptRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * @resource 包裹運送狀態
 *
 * 物流包裹運送狀態相關API
 *
 */
class PackageInfoController extends Controller
{
    protected $postRepo;

    public function __construct(PackageInfoRepository $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    /**
     * @api {get} /api/PackageInfo   取得物流包裹列表
     * @apiVersion 1.0.0
     * @apiName PackageInfo
     * @apiGroup Package
     * @apiDescription 取得物流包裹列表
     */

    /**
     * @return \Illuminate\Http\Response
     */
    public function index($communityId, $userId)
    {
        $posts = $this->postRepo->index();

        if (!$posts) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => $posts]);
    }

//    /**
//     * Show the form for creating a new resource.
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function create()
//    {
//        //
//    }

    /**
     * @api {post} /api/PackageInfo   新增物流包裹
     * @apiVersion 1.0.0
     * @apiName PackageInfo
     * @apiGroup Package
     * @apiDescription 新增物流包裹
     *
     * @apiParam {Integer} [community_id] 社區ID
     * @apiParam {Integer} [user_id] 使用者ID
     * @apiParam {String} [customer_no] 客戶編號
     * @apiParam {String} [piece] 件數
     * @apiParam {String} [carton_size] 材積
     */

    /**
     * @param  AddPackageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddPackageRequest $request)
    {
        $data = $this->postRepo->create(
            $request->only(
                'community_id',
                'user_id',
                'tracking_pick_up_id',
                'customer_no',
                'flag',
                'piece',
                'carton_size'
            )
        );

        if (!$data) {
            return response()->json(['errorCode' => 504, 'errorMessage' => 'create error'], 403);
        }

        return response()->json(['message' => 'create success.']);
    }

//    /**
//     * Display the specified resource.
//     *
//     * @param  int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function show($id)
//    {
//        // 關閉get data from id
//
//        $post = $this->postRepo->find($id);
//
//        if (!$post) {
//            return response()->json(['data' => null]);
//        }
//
//        $trackingDataId = array_get($post, 'tracking_pick_up_id');
//        $trackingNumber = array_get($post, 'tracking_number');
//
//        // get customer_no
//        $trackingData = (new \App\Repositories\TrackingPickUpReceiptRepository)->find($trackingDataId);
//        $customerNo = array_get($trackingData, 'customer_no');
//
//        $data = self::getPackageStatusByTrackingId($trackingDataId, $customerNo, $trackingNumber);
//
//        if ($data == null) {
//            return response()->json(['data' => $post]);
//        }
//
//        return response()->json(['data' => $this->postRepo->find($id)]);
//    }

//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param  int  $id
//     * @return \Illuminate$postTrackingRequest\Http\Response
//     */
//    public function edit($id)
//    {
//        //
//    }

    /**
     * 更新物流包裹
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = $this->postRepo->find($id);

        if (!$post) {
            return response()->json(['status' => 1, 'message' => 'post not found'], 404);
        }

        return response()->json(['status' => 0, 'post' => $post]);
    }

    /**
     * 刪除物流包裹
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->postRepo->destroy($id);

        if (!$result) {
            return response()->json(['status' => 1, 'message' => 'post not found'], 404);
        }

        return response()->json(['status' => 0, 'message' => 'success']);
    }

    public static function findAsTrackingPickupId($id, $isGetInfo = false)
    {
        if ($isGetInfo) {
            return DB::table('package_infos')
                ->select('*')
                ->where('tracking_pick_up_id', '=', $id)
                ->get();
        } else {
            return DB::table('package_infos')
                ->select('piece', 'carton_size')
                ->where('tracking_pick_up_id', '=', $id)
                ->get();
        }
    }

    public static function updateAsTrackingPickupId($id, $request)
    {
        return DB::table('package_infos')
            ->where('tracking_pick_up_id', '=', $id)
            ->update($request);
    }

    /**
     * @api {get} /api/PackageInfoByTrackingId/:$trackingId   取得物流包裹配送狀態
     * @apiVersion 1.0.0
     * @apiName PackageInfoByTrackingId
     * @apiGroup Package
     * @apiDescription 取得物流包裹配送狀態
     *
     * @apiParam {Number} id Tracking unique ID.
     *
     */

    /**
     * @param $trackingId
     *      宅配單 Id
     * @return \Illuminate\Support\Collection
     *      列表
     */
    public static function getPackageInfoByTrackingId($communityId, $userId, $trackingId)
    {
        $result = self::findAsTrackingPickupId($trackingId, true);
        if (!$result) {
            return $result;
        }

        $trackingNumber = array_get($result, 'tracking_number');

        // get customer_no
        $trackingData = (new \App\Repositories\TrackingPickUpReceiptRepository)->find($trackingId);
        $customerNo = array_get($trackingData, 'customer_no');

        self::getPackageStatusById($trackingId, $customerNo, $trackingNumber);

        return self::findAsTrackingPickupId($trackingId,true);
    }

    public static function getPackageStatusById($trackingId, $customerNo, $trackingNumber)
    {
        $requestData = array();
        // mapping custom_no
        $requestData = array_add($requestData, 'customer_no', $customerNo);
        $requestData = array_add($requestData, 'tracking_number', $trackingNumber);

        $postTrackingRequest = (new KerryPickUpController)->postTrackingRequest($requestData);

        // post data to Kerry Tracking
        if (!$postTrackingRequest) {
            return response()->json(['errorCode' => 999, 'errorMessage' => $postTrackingRequest], 403);
        }

        // response get JSON
        $jsonData = json_decode($postTrackingRequest, true);

        if ($jsonData['Data'] != null) {
            // rename
            $tempTracking = array();

            $tempTracking = array_add($tempTracking, 'receive_date', $jsonData['Data'][0]['ReceiveDate']);
            $tempTracking = array_add($tempTracking, 'receive_time', $jsonData['Data'][0]['ReceiveTime']);
            $tempTracking = array_add($tempTracking, 'tracking_number', $jsonData['Data'][0]['TrackingNumber']);
            $tempTracking = array_add($tempTracking, 'pick_up_no', $jsonData['Data'][0]['PICKUP_NO']);
            $tempTracking = array_add($tempTracking, 'status', $jsonData['Data'][0]['Status']);
            $tempTracking = array_add($tempTracking, 'station', $jsonData['Data'][0]['Station']);
            $tempTracking = array_add($tempTracking, 'message', $jsonData['Data'][0]['Message']);

            self::updateAsTrackingPickupId($trackingId, $tempTracking);

            return response()->json(['data' => self::findAsTrackingPickupId($trackingId, true)]);
        } else {
            if ($jsonData['ErrorData'] != null) {
                return response()->json(['errorCode' => 506, 'errorMessage' => $jsonData['ErrorData']], 404);
            } else {
                return response()->json($postTrackingRequest, 404);
            }
        }
    }

    /**
     * @api {post} /api/RefreshPackageInfo  更新物流包裹運送狀態 From 大榮
     * @apiVersion 1.0.0
     * @apiName RefreshPackageInfo
     * @apiGroup Package
     * @apiDescription 更新物流包裹運送狀態 From 大榮
     *
     * @apiParam {Number} id Tracking unique ID.
     *
     */

    /**
     * @param KerryPackageInfoRequest $request
     *      參數
     * @return \Illuminate\Http\JsonResponse
     *      列表
     */
    public static function refreshPackageInfo(KerryPackageInfoRequest $request, $communityId, $userId)
    {
        $request->only(
            'tracking_id',
            'customer_no',
            'tracking_number'
        );

        $trackingId = $request->get('tracking_id');

        if (!$request) {
            return response()->json(['errorCode' => 505, 'errorMessage' => '參數不足'], 403);
        }

        return self::getPackageStatusById($trackingId, $request->get('customer_no'), $request->get('tracking_number'));
    }
    
}
