<?php

namespace Info\ComplaintBundle\Controller;

use Application\Sonata\MediaBundle\Entity\Gallery;
use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;
use Application\Sonata\MediaBundle\Entity\Media;
use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Entity\ComplaintsCommentRating;
use Info\CommentBundle\Entity\Comment;
use Info\ComplaintBundle\Form\ComplaintType;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemOrphanageStorage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

class ComplaintController extends Controller
{

    public function createComplaintAction(Request $request, $id)
    {
        $complaint = new Complaint();
        $company = null;
        if ($id != null)
        {
            $company = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company')->find($id);
            $complaint->setCompany($company);
        }
        $form = $this->createForm(new ComplaintType(), $complaint);
        if ($request->isMethod('post')) {
            $form->submit($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $complaint->setCreated (new \DateTime());
                $complaint->setAuthor($this->getUser());
                if(is_null($complaint->getCompany())){

                    $company = new Company();
                    $em = $this->getDoctrine()->getManager();
                    $company->setName($request->get('company'));
                    $em->persist($company);
                    $complaint->setCompany($company);
                }
                /** @var FilesystemOrphanageStorage $manager */
                $manager = $this->get('oneup_uploader.orphanage_manager')->get('gallery');
                $files = iterator_to_array($manager->getFiles());
                // upload all files to the configured storage
                if (!empty($files)) {
                    $gallery = new Gallery();
                    $gallery->setName("Complaint gallery");
                    $gallery->setDefaultFormat("default_big");
                    $gallery->setContext("complaint");
                    $gallery->setEnabled(true);
                    $gallery->setCreatedAt(new \DateTime());
                    /** @var SplFileInfo $file */
                    foreach ($files as $i=>$file) {
                        $media = new Media();
                        $media->setName($file->getBasename());
                        $media->setEnabled(true);
                        $media->setBinaryContent($file->getRealPath());
                        $media->setContext('complaint');
                        $media->setProviderName('sonata.media.provider.image');
                        $mediaManager = $this->get("sonata.media.manager.media");
                        $mediaManager->save($media);
                        unlink($file->getRealPath()); //удаляем временные файлы

                        $galleryHasMedia = new GalleryHasMedia();
                        $galleryHasMedia->setGallery($gallery);
                        $galleryHasMedia->setMedia($media);
                        $galleryHasMedia->setEnabled(true);
                        $galleryHasMedia->setPosition($i);
                        $galleryHasMedia->setCreatedAt(new \DateTime());
                        $em->persist($galleryHasMedia);
                        $gallery->addGalleryHasMedias($galleryHasMedia);
                    }
                    $em->persist($gallery);
                    $complaint->setGallery($gallery);
                }
                $em->persist($complaint);
                $em->flush();
                $this->get('info_complaint.service.notification_service')->notifyManager($complaint);
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('complaint'=>$this->renderView("InfoComplaintBundle:Complaint/_blockComplaint:_complaintItemInCompanyPage.html.twig", array('complaint'=>$complaint))));
                }
                return $this->redirect($this->generateUrl('info_complaint_complaint',array('id'=>$complaint->getId())));
            }
        }

        return $this->render('InfoComplaintBundle:Complaint:create_complaint.html.twig',
            array('form' => $form->createView(), 'company'=>$company)
        );
    }

    public function showComplaintAction(Request $request, Complaint $complaint)
    {
        // BREADCRUMBS
        $breadcrumbManager = $this->get('bcm_breadcrumb.manager');
        $breadcrumbManager->addItem($request->get('_route'), "Отзыв", array('id' => $complaint->getId()));
        $company = $complaint->getCompany();
        $breadcrumbManager->addBreadcrumbsForRoute('info_company_homepage',
            array(
                'company_name' => $company->getName(),
                'slug' => $complaint->getCompany()->getSlug()
            ));
        // END BREADCRUMBS

        // SEO
        $seoPage = $this->container->get('sonata.seo.page');

        $title = sprintf("%s оставил отзыв на компанию %s", $complaint->getAuthor(), $company->getName());
        $description = $complaint->getText();
        $seoPage
            ->setTitle($title)
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', $title)
            ->addMeta('property', 'og:type', 'website')
            ->addMeta('property', 'og:url',  $request->getUri())
            ->addMeta('property', 'og:description', $description)
        ;
        // END SEO

        // COMMENTS

        $commentRep = $this->getDoctrine()->getRepository("InfoCommentBundle:Comment");
        $nodes = $commentRep->getRootCommentsByComplaint($complaint);
        $options = array(
            'decorate' => true,
            'rootOpen' => function($tree) {
                if(count($tree) && ($tree[0]['lvl'] == 0)){
                    return '';
                } else {
                    return '<ul class="sub-comments">';
                }
            },
            'rootClose' => function($tree) {
                if(count($tree) && ($tree[0]['lvl'] == 0)){
                    return '';
                } else {
                    return '</ul>';
                }
            },
            'childOpen' => '<li class="sub-li-comments">',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) use (&$complaint, &$commentRep)  {
                    return $this->renderView('InfoCommentBundle:Default:comment.html.twig',array('node'=>$node,'complaint'=>$complaint,'user'=>$commentRep->find($node['id'])->getUser()));
                }
        );

        $htmlTree = array();
        foreach($nodes as $node)
            $htmlTree[] = $commentRep->childrenHierarchy(
                $node, /* starting from root nodes */
                false, /* true: load all children, false: only direct */
                $options,
                true
            );

        //ENDCOMMENTS

        return $this->render('InfoComplaintBundle:Complaint:complaint.html.twig', array(
            'complaint' => $complaint,
            'treeComments'   => $htmlTree
        ));
    }

    public function lastAddedComplaintsAction(Request $request)
    {
        $page = $request->get('page', 0);
        $itemCount = 10;
        $complaints = $this->getDoctrine()
            ->getRepository('InfoComplaintBundle:Complaint')
            ->findBy(array(),array('id'=>'desc'), $itemCount, $page * $itemCount);

        $page++;
        if ($request->isXmlHttpRequest()) {
            return $this->render('InfoComplaintBundle:Complaint:complaints_list.html.twig', array('complaints' => $complaints, 'page' => $page));
        } else {
            return $this->render('InfoComplaintBundle:Complaint:last_complaints_list.html.twig', array('complaints' => $complaints, 'page' => $page));
        }
    }

    public function deleteComplaintAction(Request $request, Complaint $complaint)
    {
        if ($complaint == null)
        {
            return $this->createNotFoundException();
        }

        if (
            ($complaint->getAuthor() == null || $complaint->getAuthor() != $this->getUser()) &&
            !$this->get('security.context')->isGranted('ROLE_MODERATOR')
        ) {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }

        $em = $this->getDoctrine()->getManager();
        if (!is_null($complaint->getGallery())) {
            $mediaManager = $this->get("sonata.media.manager.media");
            foreach ($complaint->getGallery()->getGalleryHasMedias() as $galleryMedia) {
                $media = $galleryMedia->getMedia();
                $provider = $this->get($media->getProviderName());
                $provider->removeThumbnails($media);
                $em->remove($galleryMedia);
                $mediaManager->delete($media);
            }
        }

        $companySlug = $complaint->getCompany()->getSlug();
        $em->remove($complaint);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('success' => true), 200);
        }
        return $this->redirect($this->generateUrl('info_company_homepage', array('slug'=> $companySlug)));
    }

    public function editComplaintAction(Complaint $complaint)
    {
        if ($complaint == null)
        {
            return $this->createNotFoundException();
        }

        if ($complaint->getAuthor() == null || $complaint->getAuthor()!=$this->getUser() )
        {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }

        $form = $this->createForm( new ComplaintType(),$complaint);
        $form->remove('company');

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            $id = $complaint->getId();
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($complaint);
                $em->flush();
                $this->container->get('session')->getFlashBag()->add('complaint_edit_success', 'Профиль отзывов обновлен');
                return $this->redirect($this->generateUrl('info_company_homepage', array('slug'=> $complaint->getCompany()->getSlug())));
            } else {
                $this->container->get('session')->getFlashBag()->add('complaint_edit_error', 'Профиль отзывов не сохранен, обнаружена ошибка');
                return $this->redirect($this->generateUrl('info_complaint_edit',array('complaint'=>$id)));
            }
        }

        return $this->render('InfoComplaintBundle:Complaint:edit_complaint.html.twig',array('form'=>$form->createView(), 'complaint'=>$complaint));
    }

    public function voteAction(Request $request, $type, $id, $voteType)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $complaintsCommentRatingRep = $this->getDoctrine()->getRepository("InfoComplaintBundle:ComplaintsCommentRating");
        $element = null;
        $cookie = $request->cookies->get('anonymous-vote');
        $ip = $request->getClientIp();

        if ($voteType == 'plus') {
            $voteValue = 1;
        } else if ($voteType == 'minus'){
            $voteValue = -1;
        } else {
            return new JsonResponse(array('error' => "Неверный запрос"), 400);
        }

        if ($type == 'complaint') {
            $element = $entityManager->getRepository('InfoComplaintBundle:Complaint')->find($id);
        } elseif ($type == 'comment') {
            $element = $entityManager->getRepository('InfoCommentBundle:Comment')->find($id);
        } else {
            return new JsonResponse(array('error' => "Неверный запрос"), 400);
        }

        if ($element == null) {
            return new JsonResponse(array('error' => "Элемент не найден"), 404);
        }


        if ($this->getUser()) {
            $vote = $complaintsCommentRatingRep->findOneBy(array('elementId'=> $id, 'type' => $type, 'author' => $this->getUser()));
        } else {
            $vote = $complaintsCommentRatingRep->findOneBy(array('elementId' => $id, 'sessionCookie' => $cookie, 'ip' => $ip));
        }

        if ($vote === null) {
            $newComplaintsCommentRating = new ComplaintsCommentRating();
            $newComplaintsCommentRating->setType($type);
            $newComplaintsCommentRating->setElementId($id);
            $newComplaintsCommentRating->setAuthor($this->getUser());
            $newComplaintsCommentRating->setSessionCookie($cookie);
            $newComplaintsCommentRating->setIp($ip);
            $newComplaintsCommentRating->setVote($voteValue);

            $element->setVote($element->getVote() + $voteValue);

            $entityManager->persist($newComplaintsCommentRating);
        } else {

            if ($vote->getVote() == $voteValue) {
                return new JsonResponse(array('error' => "Вы уже голосовали"), 400);
            }

            $element->setVote($element->getVote() - $vote->getVote() + $voteValue);
            $vote->setVote($voteValue);
            $entityManager->persist($vote);
        }

        $entityManager->persist($element);
        $entityManager->flush();
        return new JsonResponse(array('voteValue' => $element->getVote()));
    }
}
