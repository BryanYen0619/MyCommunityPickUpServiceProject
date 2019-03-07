<?php

namespace App\Http\Controllers\api;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Request;

class KerryPickUpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function postPickUpRequest($inputData)
    {
        // rename inputData sub array pickup_content
        $tempPickUpContent = array_get($inputData, 'pickup_content');
        $tempPickUpContent = array_map(function($tempPickUpContent) {
            return array(
                'Piece' => $tempPickUpContent['piece'],
                'CartonSize' => $tempPickUpContent['carton_size']
            );
        }, $tempPickUpContent);

        // get current date
        $objDateTime = new DateTime('NOW');

        // mapping POST data
        $postData = array([
            'CustomerNo' => array_get($inputData, 'customer_no'),
            'PICKUP_NO' => array_get($inputData, 'pick_up_no'),
            'Shipper' => array_get($inputData, 'shipper'),
            'ShipperPhone' => array_get($inputData, 'shipper_phone'),
            'ShipperPost' => array_get($inputData, 'shipper_post'),
            'ShipperAdd' => array_get($inputData, 'shipper_address'),
            'Consignee' => array_get($inputData, 'consignee'),
            'ConsigneePhone' => array_get($inputData, 'consignee_phone'),
            'ConsigneePost' => array_get($inputData, 'consignee_post'),
            'ConsigneeAdd' => array_get($inputData, 'consignee_address'),
            'ETP' => array_get($inputData, 'transport_date'),
            'ETA' => array_get($inputData, 'delivery_period'),
            'Remark' => array_get($inputData, 'remark'),
            'Flag' => md5(array_get($inputData, 'customer_no') . $objDateTime->format('Ymd')),
            'PickupContent' => $tempPickUpContent
        ]);

        $client = new Client();

        // set Header
        $headers = array(
            'Content-type' => 'application/json; charset=utf-8',
            'Accept'=> 'application/json'
        );

        $url = 'http://api.kerrytj.com/CommunityPickup/api/Request/PickupRequest';

        $request = new Request("POST", $url, $headers, json_encode($postData));
        try {
            $response = $client->send($request, ['timeout' => 30]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                // Error
                $response = response()->json(["Request To Server Error."]);
            } else {
                $response = $response->getBody()->getContents();
            }
        } catch (GuzzleException $e) {
            $response = response()->json(["Request To Server Error"=> $e->getMessage()]);
        }

        return $response;
    }

    public function postTrackingRequest($inputData)
    {
        // get current date
        $objDateTime = new DateTime('NOW');

        // mapping POST data
        $postData = array(
            'TrackingNumber' => array_get($inputData, 'tracking_number'),
            'CustomerNo' => array_get($inputData, 'customer_no'),
            'Flag' => md5(array_get($inputData, 'customer_no') . $objDateTime->format('Ymd')),
        );

        $client = new Client();

        // set Header
        $headers = array(
            'Content-type' => 'application/json; charset=utf-8',
            'Accept'=> 'application/json'
        );

        $url = 'http://api.kerrytj.com/CommunityPickup/api/Request/TrackingRequest';

        $request = new Request("POST", $url, $headers, json_encode($postData));
        try {
            $response = $client->send($request, ['timeout' => 30]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                // Error
                $response = response()->json(["Request To Server Error."]);
            } else {
                $response = $response->getBody()->getContents();
            }
        } catch (GuzzleException $e) {
            $response = response()->json(["Request To Server Error"=> $e->getMessage()]);
        }

        return $response;
    }
}
