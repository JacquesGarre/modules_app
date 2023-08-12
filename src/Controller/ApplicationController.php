<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ModuleRepository;
use App\Repository\ListingRepository;
use App\Repository\PageRepository;

class ApplicationController extends AbstractController
{
    #[Route('/{uri}', name: 'app_application_page')]
    public function index(
        string $uri,
        PageRepository $pageRepository
    ): Response
    {
        $page = $pageRepository->findOneBy(['uri' => $uri]);
        return $this->render('_application/page.html.twig', [
            'page' => $page
        ]);
    }
}
