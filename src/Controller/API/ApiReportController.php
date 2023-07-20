<?php

namespace App\Controller\API;

use App\Entity\Reports;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiReportController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createReport(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        $title = $content['title'];
        $description = $content['description'];

        $report = new Reports();
        $report->setType(1)
            ->setTitle($title)
            ->setDescription($description)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUid("thisIsAUID")
            ->setReportedUid("ThisIsAnotherUID")
        ;

        $this->em->persist($report);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'report' => $report
        ]);
    }
}
