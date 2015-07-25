<?php

namespace Info\CommentBundle\Controller;

use Info\CommentBundle\Form\CommentType;
use Info\ComplaintBundle\Entity\Complaint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Info\CommentBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function formAction($complaint)
    {
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment, array(
            'action' => $this->generateUrl('info_comment_create', array('complaint' => $complaint))
        ));
        return $this->render('InfoCommentBundle:Default:form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function createAction(Request $request, Complaint $complaint)
    {
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setComplaint($complaint);
            $em->persist($comment);
            $em->flush();
            $this->getNotificationService()->notifyComplaintAuthor($comment);

            return $this->render('InfoCommentBundle:Default:comment.html.twig',
                array('node' => $comment, 'complaint' => $complaint, 'user' => $this->getUser())
            );
        }
        return new JsonResponse(array('msg'=>$form->getErrorsAsString()), 400);
    }

    public function replyAction(Request $request, Complaint $complaint, Comment $comment)
    {
        $commentContent = $request->request->get('comment');
        if ($request->isMethod("POST") && $commentContent) {
            $em = $this->getDoctrine()->getManager();

            $newComment = new Comment();
            $newComment->setUser($this->getUser());
            $newComment->setParent($comment);
            $newComment->setComment($commentContent);
            $newComment->setComplaint($complaint);
            $em->persist($newComment);
            $em->flush();
            $this->getNotificationService()->notifyComplaintAuthor($newComment);
            $this->getNotificationService()->notifyCommentAuthor($newComment);

            return $this->render('InfoCommentBundle:Default:comment.html.twig',
                array('node' => $newComment, 'complaint' => $complaint, 'user' => $this->getUser())
            );
        }
        return new JsonResponse(array('msg'=>'Неправильный формат данных'), 400);
    }

    public function editAction(Request $request, Comment $comment) {
        if ($comment == null) {
            return $this->createNotFoundException();
        }
        if (($comment->getUser() == null || $comment->getUser() != $this->getUser()) && !$this->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }
        $commentContent = $request->request->get('comment');
        if ($request->isMethod("POST") && $commentContent) {
            $em = $this->getDoctrine()->getManager();

            $comment->setComment($commentContent);
            $em->flush();

            return new JsonResponse(array('msg'=>'Комментраий изменен.'));
        }
        return new JsonResponse(array('msg'=>'Неправильный формат данных'), 400);
    }

    public function deleteAction(Request $request, Comment $comment) {
        if ($comment == null) {
            return $this->createNotFoundException();
        }

        if (($comment->getUser() == null || $comment->getUser() != $this->getUser()) && !$this->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('InfoCommentBundle:Comment');
        $companySlug = $comment->getComplaint()->getCompany()->getSlug();
        $remove = 0;
        if ($repository->childCount($comment)) {
            $comment->setDeletedAt(new \DateTime());
        } else {
            $em->remove($comment);
            $remove = 1;
        }
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('success' => true, 'remove'=>$remove), 200);
        }
        return $this->redirect($this->generateUrl('info_company_homepage', array('slug'=> $companySlug)));
    }

    private function getNotificationService()
    {
        return $this->get('info_complaint.service.notification_service');
    }
}