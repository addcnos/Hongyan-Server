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
    'arrivals' => [
        'Msg:Txt',
    ],
    'msg_count' => 'im_msg_count',
    'last_msg' => 'im_last_msg',
    'last_msg_set' => 'im_last_msg_set',
    'token_uid' => 'im_token_uid',
    'uid_token' => 'im_uid_token',
    'del_key' => 'im_del_key',
    'client_id_uid' => 'im_client_id_uid:', // websocket连接时保存clientId与uid的对应关系到redis，用于onclose时发送下线通知
    'token_expire_time' => env('TOKEN_EXPIRE_TIME', 2592000),
    'liaison_person' => env('IM_LIAISON_PERSON', 15552000),
    'last_read' => 'im_last_read',
    'chat_users_list' => 'im_chat_users:', //用戶的好友列表 redis key
    'config_cache' => 'im:config:hash:cache:', // 配置数据缓存key

    /* im_config 表的配置key start */
    'msg_words_blacklist' => 'msg_words_blacklist', // 消息黑名单关键词
    /* im_config 表的配置key end */
];

