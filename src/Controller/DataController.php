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
use Exception;

class DataController extends AbstractController
{
    public function __construct(private DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    #[Route('/data/add/{moduleId}', name: 'app_data_add')]
    public function add(int $moduleId, ModalFormService $modal, Request $request, ModuleRepository $moduleRepository): Response
    {
        $errors = [];
        try {
            $module = $moduleRepository->findOneBy(['id' => $moduleId]);
            $args = $request->request->all()[$module->getSqlTable()];
            $this->dataService->insert($module->getSqlTable(), $args);
        } catch(Exception $e) {
            $errors[$module->getSqlTable()][] = $e->getMessage();
            return new Response(json_encode([
                'errors' => $errors
            ]));
        }

        return new Response(json_encode([
            'success' => 'Entity added'
        ]));
       
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
        $this->dataService->delete($module->getSqlTable(), $id);
        return new Response(json_encode([
            'success' => $module->getLabelSingular().' deleted'
        ]));
    }

}
