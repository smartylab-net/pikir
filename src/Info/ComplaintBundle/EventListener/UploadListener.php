<?php
namespace Info\ComplaintBundle\EventListener;

use Application\Sonata\MediaBundle\Entity\Media;
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

    public function onUpload(PostPersistEvent $event)
    {
        $request    = $event->getRequest();
        $response   = $event->getResponse();
        $file       = $event->getFile();

        /** @var FilesystemOrphanageStorage $manager */
        $manager = $this->container->get('oneup_uploader.orphanage_manager')->get('gallery');
        $files = iterator_to_array($manager->getFiles());

        foreach ($files as $i=>$file) {

        }

        /*$response['files'] = array(
            'name' => $file->getFileName(),
            'size' => $file->getSize(),
            'url'  => 'http://hack.home/uploads/gallery/' . $file->getFileName(),
            'delete_url'   => $delete,
        );*/
    }
}