<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TableRepository;
use App\Repository\ModuleRepository;
use App\Entity\Table;
use App\Service\DataService;

class TableController extends AbstractController
{

    #[Route('/administration/tables', name: 'app_table_index')]
    public function index(TableRepository $tableRepository): Response
    {
        $tables = $tableRepository->findAll();
        return $this->render('table/_table.html.twig', [
            'tables' => $tables,
        ]);
    }

    #[Route('/administration/tables/add/{moduleId}', name: 'app_table_add')]
    public function add(int $moduleId, ModalFormService $modal, Request $request, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['id' => $moduleId]);
        $table = new Table();
        $table->setModule($module);

        $params = [
            'moduleId' => $moduleId
        ];

        return $modal->show(
            $title = 'Create a new table for '.$module->getLabelSingular(),
            $class = 'table',
            $route = 'app_table_add',
            $request,
            $table,
            'POST',
            $params
        );
    }

    #[Route('/administration/tables/{id}/edit', name: 'app_table_edit')]
    public function edit(ModalFormService $modal, Request $request, int $id, TableRepository $tableRepository): Response
    {
        $table = $tableRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];

        return $modal->show(
            $title = 'Edit table '.$table->getTitle(),
            $class = 'table',
            $route = 'app_table_edit',
            $request,
            $table,
            'POST',
            $params
        );
    }

    #[Route('/administration/tables/{id}/delete', name: 'app_table_delete')]
    public function delete(ModalFormService $modal, Request $request, int $id, TableRepository $tableRepository): Response
    {
        $table = $tableRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];
        return $modal->show(
            $title = 'Delete table '.$table->getTitle(),
            $class = 'table',
            $route = 'app_table_delete',
            $request,
            $table,
            $method = 'DELETE',
            $params
        );
    }

    #[Route('/administration/tables/{id}', name: 'app_table_show')]
    public function show(
        int $id,
        TableRepository $tableRepository
    ): Response
    {
        $table = $tableRepository->findOneBy(['id' => $id]);
        return $this->render('table/show.html.twig', [
            'table' => $table
        ]);
    }

}
