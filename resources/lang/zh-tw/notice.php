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
return [
    'sign_error' => '簽名參數異常',
    'online_notice' => '上線',

    'success' => ['code' => 200, 'message' => '請求成功'],
    'do_success' => ['code' => 200, 'message' => '操作成功'],
    'server_error' => ['code' => 5003, 'message' => '系統異常，請稍後再試'],

    'failed' => ['code' => 4001, 'message' => '請求失敗'],
    'do_failed' => ['code' => 4002, 'message' => '操作失敗'],
    'parameter_error' => ['code' => 4003, 'message' => '參數錯誤'],
    'system_error' => ['code' => 4004, 'message' => '系統錯誤'],
    'sign_error' => ['code' => 4005, 'message' => '驗簽失敗'],
    'user_not_exists' => ['code' => 4006, 'message' => '用戶不存在'],
    'uid_invalid' => ['code' => 4007, 'message' => '無效用戶'],
    'key_error' => ['code' => 4008, 'message' => '無效key'],
    'ext_error' => ['code' => 4009, 'message' => '只能上傳圖片'],
    'size_error' => ['code' => 4010, 'message' => '文件大小限制'],
    'account_disabled' => ['code' => 4011, 'message' => '賬號被禁用'],
    'uid_error' => ['code' => 4012, 'message' => '用戶ID不正確'],
    'ws_close' => ['code' => 4013, 'message' => '連接已斷開'],
    'none_edit' => ['code' => 4014, 'message' => '沒有數據被修改'],
    'msg_in_blacklist' => ['code' => 4016, 'message' => '系統異常！'],    // 消息有黑名单关键词

    'message' => [
        'send_success' => ['code' => 200, 'message' => '消息發送成功'],
    ],

    'too_many_attempts' => ['code' => 429, 'message' => '請求太快了，請稍後再試', 'data' => null],
];
