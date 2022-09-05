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
namespace App\Services\Messages;

use Addcnos\GatewayWorker\Client;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use JsonSerializable;

class Message implements MessageInterface, ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    public $data = [];

    protected $type = 'unknown';

    public function __construct(array $data = [])
    {
        Client::$registerAddress = config('gatewayworker.register_address');

        // 自动生成ID
        if (empty($data['msg_id'])) {
            $data['msg_id'] = (string) Str::uuid();
        }

        // todo 定义默认消息
        $this->data = array_merge([
            'msg_id' => '',
            'from_uid' => '',
            'target_uid' => '',
            'type' => $this->type ?? 'Msg:Txt',
            'content' => '',
            'send_time' => date('Y-m-d H:i:s'),
            'status' => 1, //0待推；1已推；2已送达；3已读
            'arrivals_callback' => 1, //消息是否需要回传,0否1是
            'message_direction' => 2, //1是自己发出的消息,2是接收的消息
        ], $data);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __unset($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return $this->data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function is()
    {
        if (func_num_args() > 0) {
            $patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $this->type)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    public function sendToUid($uid)
    {
        Client::isUidOnline($uid) && Client::sendToUid($uid, $this->toJson());
    }

    public function sendToClient($clientId)
    {
        Client::isOnline($clientId) && Client::sendToClient($clientId, $this->toJson());
    }

    public function sendToGroup($group)
    {
        Client::sendToGroup($group, $this->toJson());
    }

    public static function create(array $data = [])
    {
        switch ($data['type'] ?? 'msg:txt') {
            default:
            case 'Msg:Txt':
                return new MsgTxtMessage($data);
            case 'Msg:Img':
                return new MsgImgMessage($data);
            case 'Sys:Disconnect':
                return new SysDisconnect($data);
            case 'Sys:Heartbeat':
                return new SysHeartbeat($data);
            case 'Sys:MsgArrivals':
                return new SysMsgArrivals($data);
            case 'Sys:MsgRead':
                return new SysMsgRead($data);
            case 'Sys:Connect':
                return new SysConnect($data);
            case 'Sys:Customize':
                return new SysCustomize($data);
        }
    }

    // 生成會話ID
    public static function conversation($appId, $fromUid, $targetUid)
    {
        $fromId = $appId . '_' . $fromUid;
        $targetId = $appId . '_' . $targetUid;
        $conversation = [$fromId, $targetId];
        sort($conversation);
        return implode(',', $conversation);
    }
}
