<?php

namespace Markup\WistiaBundle\Twig;

use Markup\WistiaBundle\Service\Wistia;
use Psr\Log\LoggerInterface;

class WistiaExtension extends \Twig_Extension
{
    private $wistia;
    private $logger;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(
        Wistia $wistia,
        LoggerInterface $logger
    ) {
        $this->wistia = $wistia;
        $this->logger = $logger;
    }

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->twig = $environment;
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

    public function getName()
    {
        return 'markup_wistia';
    }
}
