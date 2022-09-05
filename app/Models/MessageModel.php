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
namespace App\Models;

class MessageModel extends Model
{
    protected $table = 'messages';

    protected $primaryKey = 'id';

    protected $guarded = [];

    /**
     * 消息发送者.
     * @param mixed $value
     */
    public function getFromUidAttribute($value)
    {
        return str_replace('_', '', strstr($value, '_'));
    }

    /**
     * 消息接收者.
     * @param mixed $value
     */
    public function getTargetUidAttribute($value)
    {
        return str_replace('_', '', strstr($value, '_'));
    }
}
