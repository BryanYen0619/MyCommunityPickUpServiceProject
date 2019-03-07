<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\AddTrackingRequest;
use App\Repositories\TrackingPickUpReceiptRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 *
 * @apiDefine TrackingPickUpReceipt
 * 宅配單相關API
 *
 */
class TrackingPickUpReceiptController extends Controller
{
    protected $postRepo;

    public function __construct(TrackingPickUpReceiptRepository $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    /**
     * @api {get} /api/TrackingPickUpReceipt   取得宅配單列表
     * @apiVersion 1.0.0
     * @apiName TrackingPickUpReceipt
     * @apiGroup Tracking
     * @apiDescription 取得宅配單列表
     *
     * @apiParam {Integer} [community_id] 社區ID
     * @apiParam {Integer} [user_id] 使用者ID
     *
     */

    /**
     * @return \Illuminate\Http\Response
     */
    public function index($communityId, $userId)
    {
        $sqlData = $this->postRepo->index()
            ->where('community_id', '=', $communityId)
            ->where('user_id', '=', $userId);

        return TrackingPickUpReceiptController::mappingPackageInfoDataByList($sqlData);
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
     * @api {post} /api/TrackingPickUpReceipt   新增宅配單
     * @apiVersion 1.0.0
     * @apiName TrackingPickUpReceipt
     * @apiGroup Tracking
     * @apiDescription 新增宅配單
     *
     * @apiParam {Integer} [community_id] 社區ID
     * @apiParam {Integer} [user_id] 使用者ID
     * @apiParam {String} [customer_no] 客戶編號
     * @apiParam {String} [pick_up_no] 取件編號
     * @apiParam {String} [shipper] 寄件人姓名
     * @apiParam {String} [shipper_phone] 寄件人電話
     * @apiParam {String} [shipper_post] 寄件人郵遞區號
     * @apiParam {String} [shipper_address] 寄件人地址
     * @apiParam {String} [consignee] 收件人姓名
     * @apiParam {String} [consignee_phone] 收件人電話
     * @apiParam {String} [consignee_post] 收件人郵遞區號
     * @apiParam {String} [consignee_address] 收件人地址
     * @apiParam {String} [transport_date] 指送日期
     * @apiParam {String} [delivery_period] 希望配送時段
     * @apiParam {String} [remark] 備註
     * @apiParam {JSON} [pickup_content] 包裹列表
     *
     */

    /**
     * @param  AddTrackingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $communityId, $userId)
    {
        $originData = $request->only(
            'customer_no',
            'pick_up_no',
            'shipper',
            'shipper_phone',
            'shipper_post',
            'shipper_address',
            'consignee',
            'consignee_phone',
            'consignee_post',
            'consignee_address',
            'transport_date',
            'delivery_period',
            'remark',
            'pickup_content'
        );

        $checkDeliveryPeriod = $request->get('delivery_period');
        if ($checkDeliveryPeriod != '1' && $checkDeliveryPeriod != '2' && $checkDeliveryPeriod != '4') {
            return response()->json(['errorCode' => 507, 'errorMessage' => 'delivery_period資料錯誤{ 1:上午(08-12), 2:下午(14-19), 4:不指定 }'], 404);
        }

        // post data to Kerry
        $postPickUpRequest = (new KerryPickUpController)->postPickUpRequest($originData);
        if (!$postPickUpRequest) {
            return response()->json(['errorCode' => 999, 'errorMessage' => $postPickUpRequest], 403);
        }

        // response get JSON
        $jsonData = json_decode($postPickUpRequest, true);

        if ($jsonData['Data'] != null) {
            // Add Data
            $originData = array_add($originData, 'community_id', $communityId);
            $originData = array_add($originData, 'user_id', $userId);
            // insert tracking_pick_up table
            $trackingPickUpListDataFlag = $this->postRepo->create($originData);
            if (!$trackingPickUpListDataFlag) {
                return response()->json(['errorCode' => 508, 'errorMessage' => 'create tracking error'], 404);
            }

            // insert package_info table
            $packageList = $originData['pickup_content'];
            foreach ($packageList as $item) {
                $item = array_add($item, 'community_id', $communityId);
                $item = array_add($item, 'user_id', $userId);
                $item = array_add($item, 'tracking_pick_up_id', $trackingPickUpListDataFlag->id);
                $item = array_add($item, 'tracking_number', $jsonData['Data'][0]['BLN']);

                $packageListDataFlag = (new \App\Repositories\PackageInfoRepository)->create($item);

                if (!$packageListDataFlag) {
                    return response()->json(['errorCode' => 508, 'errorMessage' => 'create package error'], 404);
                }
            }
        } else {
            if ($jsonData['ErrorData'] != null) {
                return response()->json(['errorCode' => 506, 'errorMessage' => $jsonData['ErrorData']], 404);
            } else {
                return response()->json(['errorCode' => 507, 'errorMessage' => 'response JSON Data error'], 404);
            }
        }

        return response()->json(['message' => 'create success.', 'BLN' => $jsonData['Data'][0]['BLN']]);
    }

    /**
     * @api {get} /api/TrackingPickUpReceipt/:$id   取得宅配單
     * @apiVersion 1.0.0
     * @apiName TrackingPickUpReceipt
     * @apiGroup Tracking
     * @apiDescription 取得宅配單
     *
     * @apiParam {Number} id Tracking unique ID.
     *
     */

    /**
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = $this->postRepo->find($id);

        if (!$post) {
            return response()->json(['status' => 1, 'message' => 'post not found'], 404);
        }

        return TrackingPickUpReceiptController::mappingPackageInfoData($post);

    }

//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param  int  $id
//     * @return \Illuminate\Http\Response
//     */
//    public function edit($id)
//    {
//        //
//    }

    /**
     *
     * 更新宅配單
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
     *
     * 刪除宅配單
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

    private static function mappingPackageInfoDataByList($trackingPickUpReceiptsList)
    {
        $outputDataList = $trackingPickUpReceiptsList;

        // set pickup_content data From PackageInfo
        foreach ($outputDataList as $trackingData) {
            array_add($trackingData, 'pickup_content', PackageInfoController::findAsTrackingPickupId($trackingData->id));
        }

        return response()->json(['data' => $outputDataList]);
    }

    private static function mappingPackageInfoData($trackingPickUpReceipts)
    {
        $outputData = $trackingPickUpReceipts;

        array_add($outputData, 'pickup_content', PackageInfoController::findAsTrackingPickupId($outputData->id));

        return response()->json(['data' => $outputData]);
    }

}
