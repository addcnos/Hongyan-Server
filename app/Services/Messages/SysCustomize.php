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

/**
 * 自定義的系統消息，給業務端實現一些特殊需求
 */
class SysCustomize extends Message
{
    protected $type = 'Sys:Customize';
}
