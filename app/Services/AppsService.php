<?php

declare(strict_types=1);
/**
 * This file is part of RCS.
 *
 * @link     https://github.com
 * @document https://github.com/addcnos/hongyan/blob/master/README.md
 * @license  https://github.com/addcnos/hongyan/blob/master/LICENSE
 * @author   Addcn.Inc
 * @contact  huangdijia@gmail.com
 * @contact  365039476@qq.com
 */
namespace App\Services;

use App\Models\AppsModel;

class AppsService extends BaseService
{
    /**
     * 创建应用.
     *
     * @param $name
     * @param $description
     * @return array|bool
     */
    public function create($name, $description)
    {
        $key = sha1($name . rand() . time());
        $secret = sha1($key);
        $data = compact('name', 'description', 'key', 'secret');
        $data['status'] = 1;
        $res = AppsModel::create($data);
        if ($res) {
            $this->langKey = 'notice.do_success';
            return ['key' => $key, $secret => $secret];
        }
        $this->langKey = 'notice.do_failed';
        return false;
    }

    public function getAppId($appKey, $appSecret)
    {
        $app = AppsModel::where('key', $appKey)
            ->where('secret', $appSecret)
            ->where('status', 1)
            ->select(['id'])
            ->first();
        if (! $app) {
            return false;
        }
        return $app->id;
    }
}
