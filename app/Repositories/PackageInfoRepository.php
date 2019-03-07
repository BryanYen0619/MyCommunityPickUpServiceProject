<?php
/**
 * Created by PhpStorm.
 * User: bryan.yen
 * Date: 2018/9/27
 * Time: 上午11:51
 */

namespace App\Repositories;


use App\Entities\PackageInfo;

class PackageInfoRepository
{
    public function index()
    {
        return PackageInfo::get();
    }

    public function create(array $data)
    {
        return PackageInfo::create($data);
    }

    public function find($id)
    {
        return PackageInfo::find($id);
    }

    public function delete($id)
    {
        return PackageInfo::destroy($id);
    }

    public function update($id, array $data)
    {
        $post = PackageInfo::find($id);

        if (!$post) {
            return false;
        }

        return $post->update($data);
    }
}