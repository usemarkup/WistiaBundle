<?php

namespace Markup\WistiaBundle\Service;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Markup\WistiaBundle\Cache\NullCacheItemPool;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Wistia
{
    const API_URL = 'https://api.wistia.com/v1/';
    const API_USER = 'api';

    private $apiKey;
    private $cache;
    private $timeout;

    public function __construct(

    ) {
        $this->timeout = 5;
        $this->connectTimeout = 3;
        $this->client = new GuzzleClient(['base_url' => self::API_URL]);
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function mediaShow($id)
    {
        $url = sprintf('medias/%s.json', $id);

        return $this->doRequest($url);
    }

    private function getOptions()
    {
        $o = [
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
            'auth' => [self::API_USER, $this->apiKey]
        ];

        return $o;
    }

    private function doRequest($endpointUrl)
    {
        // cache on the basis of the url...
        $cache = $this->getCache();
        $cacheKey = $endpointUrl;

        $cacheItem = $cache->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return json_decode($cacheItem->get(), $assoc = true);
        }

        $request = $this->client->createRequest('GET', $endpointUrl, $this->getOptions());

        try {
            $response = $this->client->send($request);
        } catch (GuzzleException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        if ($response->getStatusCode() != '200') {
            throw new NotFoundHttpException(
                sprintf(
                    'Wistia returned a "%s - %s" response.',
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                )
            );
        }
        //save into cache
        if ($cache instanceof CacheItemPoolInterface) {
            $cacheItem->set(json_encode($response->json()));
            $cache->save($cacheItem);
        }

        return $response->json();
    }

    /**
     * @return bool
     */
    public function mediaExists($id)
    {
        $url = sprintf('medias/%s.json', $id);

        try {
            $media = $this->doRequest($url);
            return true;
        } catch (\Exception $e) {
            // there was an exception while doing the media lookup
            // assume 404 or some other issue with wista
            return false;
        }
    }

    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    private function getCache()
    {
        if (!$this->cache instanceof CacheItemPoolInterface) {
            return new NullCacheItemPool();
        }

        return $this->cache;
    }
}
