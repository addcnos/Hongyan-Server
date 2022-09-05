<?php

declare(strict_types=1);
/**
 * This file is part of RCS.
 *
 * @link     https://github.com
 * @document https://github.com/addcnos/hongyan/blob/master/README.md
 * @license  https://github.com/addcnos/hongyan/blob/master/LICENSE
 * @author   Addcn.Inc
 */
namespace App\Http\Controllers;

class CommonController extends BaseController
{
    /**
     * @api                 {get} /common/getTimestamp 获取服务端时间戳
     * @apiGroup            Common
     * @apiName             /common/getTimestamp
     * @apiVersion          1.0.0
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": {
     *           "_timestamp":1595403944
     *       }
     *   }
     */
    public function getTimestamp()
    {
        $data = [
            '_timestamp' => time(),
        ];
        return $this->success('notice.success', $data);
    }
}
