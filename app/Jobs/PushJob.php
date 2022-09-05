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
namespace App\Jobs;

use Curl\Curl;

/**
 * 消息發送隊列.
 */
class PushJob extends Job
{
    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    public $timeout = 60;

    private $url;

    private $data;

    /**
     * Create a new job instance.
     *
     * @param mixed $url
     * @param mixed $data
     */
    public function __construct($url, $data)
    {
        $this->url = $url;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $sendData = [
            'content' => json_encode($this->data),
        ];

        $curl = new Curl();
        $curl->setTimeout(5);
        $curl->post($this->url, $sendData);

        if ($curl->error) {
            info('curl_res', ['err_code' => $curl->errorCode, 'errorMessage' => $curl->errorMessage]);
        }

        $curl->close();
    }
}
