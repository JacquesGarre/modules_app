<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ModuleRepository;
use App\Repository\FieldRepository;
use App\Repository\FormRepository;
use App\Repository\TableRepository;


class ModuleController extends AbstractController
{
    #[Route('/administration/modules/add', name: 'app_module_add')]
    public function add(ModalFormService $modal, Request $request): Response
    {
        return $modal->show(
            $title = 'Create a new entity',
            $class = 'module',
            $route = 'app_module_add',
            $request
        );
    }

    #[Route('/administration/modules/{id}/edit', name: 'app_module_edit')]
    public function edit(ModalFormService $modal, Request $request, int $id, ModuleRepository $moduleRepository): Response
    {   

        $module = $moduleRepository->findOneBy(['id' => $id]);
        $params = [];
        $params['id'] = $module->getId();
        return $modal->show(
            $title = 'Edit entity '.$module->getLabelSingular(),
            $class = 'module',
            $route = 'app_module_edit',
            $request,
            $module,
            'POST',
            $params
        );
    }

    #[Route('/administration/modules/{id}/delete', name: 'app_module_delete')]
    public function delete(ModalFormService $modal, Request $request, int $id, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['id' => $id]);
        $params = [];
        $params['id'] = $module->getId();
        return $modal->show(
            $title = 'Delete entity '.$module->getLabelSingular(),
            $class = 'module',
            $route = 'app_module_delete',
            $request,
            $module,
            $method = 'DELETE',
            $params
        );
    }

    #[Route('/administration/modules/{id}', name: 'app_module_show')]
    public function show(
        int $id,
        ModalFormService $modal,
        Request $request,
        ModuleRepository $moduleRepository,
        FieldRepository $fieldRepository,
        FormRepository $formRepository,
        TableRepository $tableRepository
    ): Response
    {
        $module = $moduleRepository->findOneBy(['id' => $id]);
        $fields = $fieldRepository->findBy(['module' => $module]);
        $forms = $formRepository->findBy(['module' => $module]);
        $tables = $tableRepository->findBy(['module' => $module]);

        $params = [];
        $params['id'] = $module->getId();
        $form = $modal->getForm('module', 'app_module_show', $module, 'POST', $params);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            return $modal->handlePostRequest($form, $module);
        }

        return $this->render('module/show.html.twig', [
            'module' => $module,
            'form' => $form,
            'fields' => $fields,
            'forms' => $forms,
            'tables' => $tables
        ]);
    }

}
