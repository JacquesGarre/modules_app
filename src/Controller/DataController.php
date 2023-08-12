<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FieldRepository;
use App\Repository\ModuleRepository;
use App\Entity\Field;
use App\Service\DataService;

class DataController extends AbstractController
{
    public function __construct(private DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    #[Route('/data/add/{moduleId}', name: 'app_data_add')]
    public function add(int $moduleId, ModalFormService $modal, Request $request, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['id' => $moduleId]);
        dd($module);
    }

    #[Route('/data/edit/{moduleId}/{id}', name: 'app_data_edit')]
    public function edit(ModalFormService $modal, Request $request, int $moduleId, int $id, ModuleRepository $moduleRepository): Response
    {   
        $module = $moduleRepository->findOneBy(['id' => $moduleId]);

        //$this->dataService->get($module->getSqlTable())

        dd($module);
    }

    #[Route('/data/delete/{moduleId}/{id}', name: 'app_data_delete')]
    public function delete(ModalFormService $modal, Request $request, int $moduleId, int $id, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['id' => $moduleId]);
        dd($module);
    }

    #[Route('/{table}/{id}', name: 'app_data_show')]
    public function show(ModalFormService $modal, Request $request, string $table, int $id, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['sqlTable' => $table]);
        return $this->render('data/show.html.twig', [
      
        ]);
    }
}
