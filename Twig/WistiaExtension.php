<?php

namespace Markup\WistiaBundle\Twig;

use Markup\WistiaBundle\Service\Wistia;

class WistiaExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(
        Wistia $wistia
    ) {
        $this->wistia = $wistia;
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
        $data = $this->wistia->mediaShow($id);

        return $data;
    }

    public function getName()
    {
        return 'markup_wistia';
    }
}
