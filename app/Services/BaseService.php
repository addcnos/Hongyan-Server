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
namespace App\Services;

use Addcnos\GatewayWorker\Client;
use App\Traits\ImCommon;

class BaseService
{
    use ImCommon;

    public $message = '';

    public $data = '';

    public $code = '';

    public $langKey = '';

    public function __construct()
    {
        Client::$registerAddress = config('gatewayworker.register_address');
    }

    public function setLangKey($langKey)
    {
        return $this->langKey = $langKey;
    }

    public function getLangKey()
    {
        return $this->langKey;
    }
}
