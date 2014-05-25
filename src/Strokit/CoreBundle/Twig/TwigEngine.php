<?php

namespace Strokit\CoreBundle\Twig;

use SunCat\MobileDetectBundle\DeviceDetector\MobileDetector;
use Symfony\Bundle\TwigBundle\TwigEngine as BaseTwigEngine;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Config\FileLocatorInterface;


class TwigEngine extends BaseTwigEngine
{
    protected $mobileDetector;

    public function __construct(\Twig_Environment $environment, TemplateNameParserInterface $parser, FileLocatorInterface $locator, MobileDetector $mobileManager)
    {
        $this->mobileDetector = $mobileManager;

        parent::__construct($environment, $parser, $locator);
    }

    public function render($name, array $parameters = array())
    {

        if ($this->mobileDetector->isMobile()) {
            $mobileTemplate = preg_replace("/^\w+:\w+:/", '$0Mobile/', $name);

            if ($this->exists($mobileTemplate)) {
                $name = $mobileTemplate;
            }
        }

        return parent::render($name, $parameters);
    }
}