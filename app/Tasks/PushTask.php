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
namespace App\Tasks;

use Curl\Curl;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class PushTask extends Task
{
    private $url;

    private $data;

    public function __construct($url, $data)
    {
        $this->url = $url;
        $this->data = $data;
    }

    public function handle()
    {
        $sendData = [
            'content' => json_encode($this->data),
        ];
//        app(Curl::class)->post($this->url, $data);
        $curl = new Curl();
        $curl->setTimeout(5);
        $curl->post($this->url, $sendData);

        if ($curl->error) {
            info('curl_res', ['err_code' => $curl->errorCode, 'errorMessage' => $curl->errorMessage]);
        }

        $curl->close();
    }
}
