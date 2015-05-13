<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            //framework bundles
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            //js Routing
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            //users
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            //sonata bundles
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            //sonata blog
            new Sonata\BlockBundle\SonataBlockBundle(),
            //news
            new Sonata\NewsBundle\SonataNewsBundle(),
            //media
            new Sonata\MediaBundle\SonataMediaBundle(),
            //sonata admin
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            //menu
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            //ckeditor
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new CoopTilleuls\Bundle\CKEditorSonataMediaBundle\CoopTilleulsCKEditorSonataMediaBundle(),
            //paginator
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            //mobile
            new SunCat\MobileDetectBundle\MobileDetectBundle(),
            //captcha
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),

            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            //application
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new Application\Sonata\NewsBundle\ApplicationSonataNewsBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),
            new Application\Sonata\FormatterBundle\ApplicationSonataFormatterBundle(),
            //Strokit Core
            new Strokit\CoreBundle\StrokitCoreBundle(),
            //PageBundle
            new Info\PageBundle\InfoPageBundle(),
            new Info\ComplaintBundle\InfoComplaintBundle(),
            new Info\CommentBundle\InfoCommentBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            //Doctrine Extension
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
