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

use App\Services\ConfigService;
use Illuminate\Http\Request;

class ConfigController extends BaseController
{
    protected $configService;

    /**
     * @apiDefine config 配置类
     */
    public function __construct(ConfigService $configService)
    {
        parent::__construct();
        $this->configService = $configService;
    }

    /**
     * @api                  {get} /config/msgWordsBlacklist 消息黑名单关键词
     * @apiGroup             config
     * @apiName              /config/msgWordsBlacklist
     * @apiVersion           1.0.0
     * @apiHeader {String}   nonce 随机数
     * @apiHeader {String}   time-stamp 时间戳
     * @apiHeader {String}   sign 签名
     * @apiHeader {String}   app-key appKey
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": {
     *          "config": {}
     *        }
     *   }
     */
    public function msgWordsBlacklist(Request $request)
    {
        $appId = $request->get('app_id');
        $config = imConfig(config('im.msg_words_blacklist'), $appId);
        $data = ['config' => $config];
        return $this->success('notice.success', $data);
    }

    /**
     * @api                  {post} /config/msgWordsBlacklist/edit 添加、编辑消息黑名单关键词
     * @apiGroup             config
     * @apiName              /config/msgWordsBlacklist/edit
     * @apiVersion           1.0.0
     * @apiHeader {String}   nonce 随机数
     * @apiHeader {String}   time-stamp 时间戳
     * @apiHeader {String}   sign 签名
     * @apiHeader {String}   app-key appKey
     * @apiParam {json}      config_value 配置数据
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": null
     *   }
     */
    public function msgWordsBlacklistEdit(Request $request)
    {
        $appId = $request->get('app_id');
        $configValue = $request->get('config_value');

        $res = $this->configService->edit(config('im.msg_words_blacklist'), $configValue, '消息黑名单关键词', $appId);
        if (! $res) {
            return $this->error('notice.do_failed');
        }
        return $this->success('notice.do_success');
    }

    /**
     * @api                  {delete} /config/delete 删除配置项
     * @apiGroup             config
     * @apiName              /config/delete
     * @apiVersion           1.0.0
     * @apiHeader {String}   nonce 随机数
     * @apiHeader {String}   time-stamp 时间戳
     * @apiHeader {String}   sign 签名
     * @apiHeader {String}   app-key appKey
     * @apiParam {String}    config_key 配置key
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": null
     *   }
     */
    public function delete(Request $request)
    {
        $appId = $request->get('app_id');
        $configKey = $request->get('config_key');

        $res = $this->configService->delete($configKey, $appId);
        if (! $res) {
            return $this->error('notice.do_failed');
        }
        return $this->success('notice.do_success');
    }
}
