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
namespace App\Traits;

trait ResponseJson
{
    /**
     * @param mixed $langKey resources/lang下定义
     * @param mixed $data
     * @return array
     */
    public function success($langKey = '', $data = null)
    {
        $lang = $langKey ? __($langKey) : [];
        return response()->json([
            'code' => ! empty($lang['code']) ? $lang['code'] : 200,
            'message' => ! empty($lang['message']) ? $lang['message'] : null,
            'data' => $data,
        ]);
    }

    /**
     * @param mixed $langKey resources/lang下定义
     * @param mixed $data
     * @return array
     */
    public function error($langKey = '', $data = null)
    {
        $lang = $langKey ? __($langKey) : [];
        return response()->json([
            'code' => ! empty($lang['code']) ? $lang['code'] : 4000,
            'message' => ! empty($lang['message']) ? $lang['message'] : null,
            'data' => $data,
        ]);
    }

    /**
     * @param mixed $message
     * @param mixed $data
     * @param int $code
     * @return array
     */
    public function successMessage($message = '', $data = null, $code = 200)
    {
        return response()->json([
            'code' => $code ?: 200,
            'message' => $message ?: null,
            'data' => $data,
        ]);
    }

    /**
     * @param mixed $message
     * @param mixed $data
     * @param int $code
     * @return array
     */
    public function errorMessage($message = '', $data = null, $code = 4000)
    {
        return response()->json([
            'code' => $code ?: 4000,
            'message' => $message ?: null,
            'data' => $data,
        ]);
    }

    /**
     * @param mixed $langKey resources/lang下定义
     * @param mixed $data
     * @param mixed $total
     * @return array
     */
    public function successWithTotal($langKey = '', $data = null, $total = 0)
    {
        $lang = $langKey ? __($langKey) : [];
        return response()->json([
            'code' => ! empty($lang['code']) ? $lang['code'] : 200,
            'message' => ! empty($lang['message']) ? $lang['message'] : null,
            'data' => $data,
            'total' => $total,
        ]);
    }
}
