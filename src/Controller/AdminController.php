<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ModuleRepository;
use App\Repository\ListingRepository;
use App\Repository\PageRepository;

class AdminController extends AbstractController
{
    #[Route('/administration', name: 'app_administration')]
    public function index(
        ModuleRepository $moduleRepository,
        ListingRepository $listingRepository,
        PageRepository $pageRepository
    ): Response
    {
        $modules = $moduleRepository->findAll();
        $listings = $listingRepository->findAll();
        $pages = $pageRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'modules' => $modules,
            'listings' => $listings,
            'pages' => $pages
        ]);
    }
}
