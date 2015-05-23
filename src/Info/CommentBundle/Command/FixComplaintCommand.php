<?php

namespace Info\CommentBundle\Command;

use Info\CommentBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FixComplaintCommand extends ContainerAwareCommand
{
    var $commentsCount = 0;
    protected function configure()
    {
        $this
            ->setName('strokit:fix:comments')
            ->setDescription('Fix comments null complaint value')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $commentRepository = $doctrine->getRepository('InfoCommentBundle:Comment');
        $comments = $commentRepository->getRootNodes();

        foreach ($comments as $comment) {
            /** @var Comment $comment */
            $complaint = $comment->getComplaint();
            $this->fix($comment, $complaint);
            $doctrine->getManager()->flush();
        }

        $output->writeln($this->commentsCount." comments fixed");
    }

    private function fix(Comment $comment, $complaint)
    {
        $comments = $comment->getChildren();
        foreach ($comments as $childComment) {
            /** @var Comment $childComment */
            $childComment->setComplaint($complaint);
            $this->commentsCount++;
            $this->fix($childComment, $complaint);
        }
    }
}