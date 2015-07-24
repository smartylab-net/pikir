<?php

namespace Info\ReportBundle\Controller;

use Info\ReportBundle\Entity\Report;
use Info\ReportBundle\Form\ReportType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function getFormAction(Request $request)
    {
        $form = $this->createForm(new ReportType(), new Report());

        return $this->render('InfoReportBundle:Default:getForm.html.twig', array('form'=>$form->createView()));
    }

    public function saveReportAction(Request $request, $type, $typeId) {
        $report = new Report();
        $form = $this->createForm(new ReportType(), $report);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $report->setUser($this->getUser());
                $em = $this->getDoctrine()->getManager();
                if ($type == 'complaint') {
                    $complaint = $em->getRepository('InfoComplaintBundle:Complaint')->find($typeId);
                    if (!$complaint) {
                        $this->createNotFoundException("Жалоба с id $typeId не найдена");
                    }
                    $report->setComplaint($complaint);
                } else {
                    $comment = $em->getRepository('InfoCommentBundle:Comment')->find($typeId);
                    if (!$comment) {
                        $this->createNotFoundException("Коммент с id $typeId не найдена");
                    }
                    $report->setComment($comment);
                }
                $em->persist($report);
                $em->flush();

                $this->getNotificationService()->notifyModerators($report);

                return new JsonResponse();
            }
        }
        return new JsonResponse(array('msg'=>'Error'), 500);
    }

    private function getNotificationService()
    {
        return $this->get('info_complaint.service.notification_service');
    }
}
