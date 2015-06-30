<?php
namespace Info\ComplaintBundle\EventListener;

use Application\Sonata\MediaBundle\Entity\Media;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemOrphanageStorage;
use Sonata\MediaBundle\Entity\MediaManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener {
    private $container;
    protected $em;

    public function __construct(ContainerInterface $container, EntityManager $em) {
        $this->container    = $container;
        $this->em           = $em;
    }

    public function onUpload(PostUploadEvent $event)
    {
        $response   = $event->getResponse();
        $file       = $event->getFile();
        $this->container->get('logger')->debug($file->getPathname());

        $webFolder = $this->container->getParameter('kernel.root_dir').'/../web';
        $response['filepath'] = $this->getRelativePath($webFolder, $file->getPathname());

    }

    function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = '/' . $relPath[0];
                }
            }
        }
    return implode('/', $relPath);
    }
}