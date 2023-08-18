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
use App\Service\FormService;
use App\Entity\Module;

class ModuleController extends AbstractController
{
    #[Route('/administration/modules', name: 'app_module_index')]
    public function index(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->findAll();
        return $this->render('module/_table.html.twig', [
            'modules' => $modules,
        ]);
    }

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
        Module $module,
        ModalFormService $modal,
        Request $request,
        ModuleRepository $moduleRepository,
        FieldRepository $fieldRepository,
        FormRepository $formRepository,
        TableRepository $tableRepository,
        FormService $formService
    ): Response
    {

        $fields = $fieldRepository->findBy(['module' => $module]);
        $forms = $formRepository->findBy(['module' => $module]);
        $tables = $tableRepository->findBy(['module' => $module]);

        $params = [];
        $params['id'] = $module->getId();

        if($request->query->get('enable')){
            $form = $formService->getForm('module', 'app_module_show', $module, 'POST', $params, 'write');
            return $this->render('includes/_form.html.twig', [
                'form' => $form,
            ]);
        }

        $form = $formService->getForm('module', 'app_module_show', $module, 'POST', $params, 'read');
        if($request->query->get('disable')){
            return $this->render('includes/_form.html.twig', [
                'form' => $form,
            ]);
        }

        if ($request->isMethod('POST')) {
            $form = $formService->getForm('module', 'app_module_show', $module, 'POST', $params, 'write');
            $form->handleRequest($request);
            return $modal->handlePostRequest($form, $module);
        }

        // Page builder
        $page = $module->getPage();
        $pageForm = false;
        if(!empty($page)){
            $params = [];
            $params['id'] = $page->getId();
    
            $pageForm = $formService->getForm('page', 'app_page_show', $page, 'POST', $params, 'read');
            if($request->query->get('enable') == 1){
                $pageForm = $formService->getForm('page', 'app_page_show', $page, 'POST', $params, 'write');
            }
            if($request->query->get('enable') == 1 || $request->query->get('disable') == 1){
                return $this->render('form/form.html.twig', [
                    'form' => $pageForm
                ]);
            }
        }


        return $this->render('module/show.html.twig', [
            'module' => $module,
            'form' => $form,
            'fields' => $fields,
            'forms' => $forms,
            'tables' => $tables,
            'pageForm' => $pageForm
        ]);
    }

}
