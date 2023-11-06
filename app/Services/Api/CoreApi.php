<?php

namespace App\Services\Api;

use App\Models\Resort;
use GuzzleHttp\Client;

class CoreApi
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    private $verb = 'get';

    private $apiKey = null;

    private $exception = \Exception::class;

    public $url;

    /**
     * @var Resort
     */
    public $resort;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param Resort $resort
     * @return void
     */
    public function setResort(Resort $resort)
    {
        $this->resort = $resort;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param $url
     * @param array $params
     * @return array
     */
    protected function getResult($url, array $params = [])
    {
        try {
            $response = $this->client->{$this->verb}(
                $url,
                [
                    'headers' => [
                        'Authorization-Token' => $this->apiKey
                    ],
                    'query'   => $params,
                ]);

            return [
                'code'       => $response->getStatusCode(),
                'data'       => json_decode($response->getBody()->getContents(), true),
            ];
        } catch (\Exception $e) {
            throw new $this->exception(
                $e->getMessage(),
                $e->getCode()
            );
        }
    }
}
