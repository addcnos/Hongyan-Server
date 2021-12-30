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
namespace App\Http\Controllers;

use App\Services\AppsService;
use Illuminate\Http\Request;

class AppsController extends BaseController
{
    /**
     * @api                 {post} /apps/create 创建应用
     * @apiGroup            Apps
     * @apiName             /apps/create
     * @apiVersion          1.0.0
     * @apiParam {String}   name 应用名称
     * @apiParam {String}   description 应用描述
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": null
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function create(Request $request, AppsService $appsService)
    {
        $name = $request->input('name', '');
        $description = $request->input('description', '');

        if (! $name) {
            return $this->error('notice.parameter_error');
        }

        $result = $appsService->create($name, $description);
        if ($result === false) {
            return $this->error($appsService->langKey);
        }
        return $this->success($appsService->langKey);
    }
}
