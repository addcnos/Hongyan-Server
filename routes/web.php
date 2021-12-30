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
 */
$router->get('/', function () {
    return 'im';
});

//创建应用
$router->post('apps/create', 'AppsController@create');

//注册
$router->post('/users/register', 'UsersController@register');
//拉黑
$router->post('/users/block', 'UsersController@block');
//解除拉黑
$router->post('/users/unBlock', 'UsersController@unBlock');
//修改用戶信息
$router->post('/users/edit', 'UsersController@edit');

//发消息
$router->group(['middleware' => ['checkMsgWordsBlacklist']], function () use ($router) {
    $router->post('/messages/send', 'MessagesController@send');
});

//从业务端发消息
$router->post('/messages/sendByApps', 'MessagesController@sendByApps');

//获取历史消息
$router->get('/messages/getHistoricalMessage', 'MessagesController@getHistoricalMessage');

//消息到达回调
$router->post('/messages/messageArrival', 'MessagesController@messageArrival');

//上线广播
$router->post('/messages/onlineNotice', 'MessagesController@onlineNotice');

//历史聊天记录同步到IM
$router->post('/messages/messageTransfer', 'MessagesController@messageTransfer');

//消息同步
$router->post('/messages/messageSynchronization', 'MessagesController@messageSynchronization');

//删除联络人
$router->post('/messages/delLiaisonPerson', 'MessagesController@delLiaisonPerson');

//图片上传
$router->post('/messages/pictureUpload', 'MessagesController@pictureUpload');

//获得联系人列表
$router->get('/chat/users', 'ChatController@users');

//设置消息已读
$router->post('/chat/readMsg', 'ChatController@readMsg');

//联系人列表在线状态
$router->get('/chat/onlineStatus', 'ChatController@onlineStatus');

//联系人列表在线状态
$router->get('/chat/onlineStatusByUids', 'ChatController@onlineStatusByUids');

//总新消息条数
$router->get('/chat/getAllNewMessage', 'ChatController@getAllNewMessage');

//删除长时间未开启的会话数据
$router->post('/chat/lastMsgClear', 'ChatController@lastMsgClear');

//获取双方信息
$router->get('/chat/getConversationInfo', 'ChatController@getConversationInfo');

//看下server状态
$router->get('/server/info', 'ServerController@info');

//获取websocket连接数
$router->get('/server/getAllUidCount', 'ServerController@getAllUidCount');

$router->get('/common/getTimestamp', 'CommonController@getTimestamp');

// 配置相关
$router->group(['prefix' => 'config', 'middleware' => ['sign']], function () use ($router) {
    $router->get('/msgWordsBlacklist', 'ConfigController@msgWordsBlacklist');
    $router->post('/msgWordsBlacklist/edit', 'ConfigController@msgWordsBlacklistEdit');
    $router->delete('/delete', 'ConfigController@delete');
});
