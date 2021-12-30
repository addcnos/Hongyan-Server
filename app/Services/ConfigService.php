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
 * @contact  imtoogle@gmail.com
 */
namespace App\Services;

use App\Models\ConfigModel;
use Illuminate\Support\Facades\Redis;

/**
 * 配置逻辑.
 */
class ConfigService extends BaseService
{
    /**
     * 获得所有配置数据.
     *
     * @param int $appId 应用ID，传0则获取通用配置
     * @return array
     */
    public function allConfig($appId = 0)
    {
        $rKey = config('im.config_cache') . $appId;
        $config = Redis::hGetAll($rKey);

        if (! $config) {
            $config = ConfigModel::where('app_id', $appId)->get(['config_key', 'config_value'])->toArray();

            if (! $config) {
                return [];
            }

            $config = array_column($config, 'config_value', 'config_key');
            Redis::hMSet($rKey, $config);
            Redis::expire($rKey, 30 * 86400);
        }

        foreach ($config as $key => $value) {
            $val = json_decode($value, true);
            $config[$key] = is_array($val) ? $val : $value;
        }

        return $config;
    }

    public function edit($configKey, $configValue, $configDesc, $appId)
    {
        $configValue = is_array($configValue) ? json_encode($configValue) : $configValue;
        $values = [
            'config_value' => $configValue,
            'config_desc' => $configDesc,
        ];
        $config = ConfigModel::query()->updateOrCreate(['config_key' => $configKey, 'app_id' => $appId], $values);
        if ($config) {
            $rKey = config('im.config_cache') . $appId;
            Redis::hSet($rKey, $configKey, $configValue);
            return true;
        }
        return false;
    }

    public function delete($configKey, $appId)
    {
        $res = ConfigModel::where('config_key', $configKey)->where('app_id', $appId)->delete();
        if ($res) {
            $rKey = config('im.config_cache') . $appId;
            Redis::hDel($rKey, $configKey);
            return true;
        }
        return false;
    }
}
