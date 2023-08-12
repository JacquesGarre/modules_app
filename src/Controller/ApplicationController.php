<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ModuleRepository;
use App\Repository\ListingRepository;
use App\Repository\PageRepository;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FieldRepository;
use App\Entity\Field;
use App\Service\DataService;

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

    #[Route('/{table}/{id}', name: 'app_data_show')]
    public function show(ModalFormService $modal, Request $request, string $table, int $id, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['sqlTable' => $table]);
        return $this->render('_application/detailed_page.html.twig', [
      
        ]);
    }
}
