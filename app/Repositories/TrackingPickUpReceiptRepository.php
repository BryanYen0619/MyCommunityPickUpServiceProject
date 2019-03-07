<?php
/**
 * Created by PhpStorm.
 * User: bryan.yen
 * Date: 2018/9/27
 * Time: 上午11:50
 */

namespace App\Repositories;

use App\Entities\TrackingPickUpReceipt;

class TrackingPickUpReceiptRepository
{
    public function index()
    {
        return TrackingPickUpReceipt::get();
    }

    public function create(array $data)
    {
        return TrackingPickUpReceipt::create($data);
    }

    public function find($id)
    {
        return TrackingPickUpReceipt::find($id);
    }

    public function delete($id)
    {
        return TrackingPickUpReceipt::destroy($id);
    }

    public function update($id, array $data)
    {
        $post = TrackingPickUpReceipt::find($id);

        if (!$post) {
            return false;
        }

        return $post->update($data);
    }
}