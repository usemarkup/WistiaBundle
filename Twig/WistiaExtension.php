<?php

namespace Markup\WistiaBundle\Twig;

use Markup\WistiaBundle\Service\Wistia;
use Psr\Log\LoggerInterface;

/**
 * Class WistiaExtension
 *
 * @package Markup\WistiaBundle\Twig
 */
class WistiaExtension extends \Twig_Extension
{
    /** @var Wistia */
    private $wistia;

    /** @var LoggerInterface */
    private $logger;

    /**
     * WistiaExtension constructor.
     *
     * @param Wistia          $wistia
     * @param LoggerInterface $logger
     */
    public function __construct(
        Wistia $wistia,
        LoggerInterface $logger
    ) {
        $this->wistia = $wistia;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'wistia_media_show',
                [$this, 'getMediaInformation'],
                ['pre_escape' => 'json', 'is_safe' => ['json']]
            )
        ];
    }

    /**
     * @param $id
     *
     * @return mixed|null
     */
    public function getMediaInformation($id)
    {
        try {
            $data = $this->wistia->mediaShow($id);

            $assets = $data['assets'];
            $keyed = [];
            foreach ($assets as $key => $attributes) {
                $keyed[$attributes['type']] = $assets[$key];
            }
            $data['assets'] = $keyed;

            return $data;
        } catch (\Exception $e) {
            $this->logger->warn(sprintf('Lookup for wistia video ID `%s` failed: %s', $id, $e->getMessage()));
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'markup_wistia';
    }
}
