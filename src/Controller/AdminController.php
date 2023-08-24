<?php

namespace App\Controller;

use App\Entity\Layout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ModuleRepository;
use App\Repository\ListingRepository;
use App\Repository\PageRepository;
use App\Repository\LayoutRepository;
class AdminController extends AbstractController
{
    #[Route('/administration', name: 'app_administration')]
    public function index(
        ModuleRepository $moduleRepository,
        ListingRepository $listingRepository,
        PageRepository $pageRepository,
        LayoutRepository $layoutRepository
    ): Response
    {
        $modules = $moduleRepository->findAll();
        $listings = $listingRepository->findAll();
        $pages = $pageRepository->findBy(['module' => null]);
        $layouts = $layoutRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'modules' => $modules,
            'listings' => $listings,
            'pages' => $pages,
            'layouts' => $layouts
        ]);
    }
}
