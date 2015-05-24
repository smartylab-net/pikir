<?php

namespace Info\ComplaintBundle\Command;

use Info\CommentBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ApplySlugCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('strokit:apply:slug')
            ->setDescription('Fix companies null slug value')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $commentRepository = $doctrine->getRepository('InfoComplaintBundle:Company');
        $companies = $commentRepository->findAll();

        foreach ($companies as $company) {
            $name = $company->getName();
            $company->setName('rand', rand(0,500));
            $doctrine->getManager()->flush();
            $company->setName($name);
            $doctrine->getManager()->flush();
        }

        $output->writeln(count($companies)." companies slug fixed");
    }
}