<?php

namespace App\Application\Controller;

use App\Domain\Service\AnalyticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(AnalyticsService $svc): Response
    {

        $data = $svc->getDashboardData();

        return $this->render(
            'dashboard.html.twig',
            $data
        );
    }

}
