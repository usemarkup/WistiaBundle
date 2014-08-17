<?php

namespace Markup\WistiaBundle\Service;

use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Wistia
{
    const API_URL = 'https://api.wistia.com/v1/';
    const API_USER = 'api';

    private $apiKey;

    public function __construct(

    ) {
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

    private function doRequest($endpointUrl, array $options = array())
    {
        // $options = $this->mergeOptions($options);
        // //only use cache if this is a Content Delivery API request
        // $cacheKey = $this->generateCacheKey($spaceData['key'], $queryType, $cacheDisambiguator, $filters);
        // $cache = $this->ensureCache($spaceData['cache']);
        // $cacheItem = $cache->getItem($cacheKey);
        // if ($api === self::CONTENT_DELIVERY_API && $cacheItem->isHit()) {
        //     return $this->buildResponseFromRaw(json_decode($cacheItem->get(), $assoc = true));
        // }

        $request = $this->client->createRequest('GET', $endpointUrl, ['auth' => [self::API_USER, $this->apiKey]]);
        //$request->setAuth(self::API_USER, $this->apiKey);
        // $request->setHeader('Authorization', hash('md5', sprintf('%s:%s', self::API_USER, $this->apiKey));

        // $this->setAuthHeaderOnRequest($request, $spaceData['access_token']);
        // $this->setApiVersionHeaderOnRequest($request, $api);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            throw new NotFoundHttpException($e->getResponse(), $exceptionMessage, 0, $e);
        }
        if ($response->getStatusCode() !== '200') {
            throw new NotFoundHttpException(
                $response,
                sprintf(
                    $exceptionMessage . ' Wistia returned a "%s - %s" response.',
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                )
            );
        }
        //save into cache
        // if ($api === self::CONTENT_DELIVERY_API) {
        //     $cacheItem->set(json_encode($response->json()));
        //     $cache->save($cacheItem);
        // }
        return $response->json();
    }

}
