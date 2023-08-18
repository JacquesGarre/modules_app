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
use App\Repository\FormRepository;
use App\Repository\TableRepository;
use App\Service\DataService;
use App\Service\FormService;
use Exception;
use stdClass;
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

    #[Route('/{table}/{id}', name: 'app_application_show_entity')]
    public function show(ModalFormService $modal, Request $request, string $table, int $id, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['sqlTable' => $table]);
        return $this->render('_application/detailed_page.html.twig', [
      
        ]);
    }

    #[Route('/{table}/edit/{id}', name: 'app_application_edit_entity')]
    public function edit(
        ModalFormService $modal, 
        Request $request, 
        string $table, 
        int $id, 
        ModuleRepository $moduleRepository, 
        DataService $dataService,
        FormRepository $formRepository
    ): Response
    {
        $module = $moduleRepository->findOneBy(['sqlTable' => $table]);
        $entity = $dataService->get($table, [], ['id' => $id])[0];

        $formEntity = $formRepository->findOneBy([
            'action' => 'edit',
            'module' => $module
        ]);

        if(empty($formEntity)){
            throw new Exception('Please create a form with action "edit" on entity '.$module->getLabelSingular());
        }

        $params = [
            'table' => $table,
            'id' => $id
        ];

        // if($request->query->get('onchange')){
        //     $formValues = $request->request->all()[$table];
        //     foreach ($formValues as $key => $value) {
        //         $setterMethod = 'set' . ucfirst($key);
        //         if (method_exists($field, $setterMethod)) {
        //             $field->{$setterMethod}($value);
        //         }
        //     }
        //     $form = $formService->getForm('field', 'app_field_edit', $field, 'POST', $params);
        //     return $this->render('includes/_form.html.twig', [
        //         'form' => $form,
        //     ]);

        // }

        return $modal->show(
            $title = 'Edit '.$module->getLabelSingular(),
            $class = $table,
            $route = 'app_application_edit_entity',
            $request,
            $entity,
            'POST',
            $params, 
            $formEntity
        );
    }

    #[Route('/{table}/delete/{id}', name: 'app_application_delete_entity')]
    public function delete(
        ModalFormService $modal,
        Request $request,
        string $table,
        int $id,
        ModuleRepository $moduleRepository,
        DataService $dataService,
        FormRepository $formRepository
    ): Response
    {
        $module = $moduleRepository->findOneBy(['sqlTable' => $table]);
        $entity = $dataService->getOneBy($table, [], ['id' => $id]);
        $params = [
            'moduleId' => $module->getId(),
            'id' => $id
        ];

        return $modal->show(
            $title = 'Delete '.$module->getLabelSingular(),
            $class = $table,
            $route = 'app_data_delete',
            $request,
            $entity,
            'POST',
            $params
        );
    }

    #[Route('/table_reload/{id}', name: 'app_application_table_reload')]
    public function tableReload(
        int $id,
        TableRepository $tableRepository
    ): Response
    {
        $table = $tableRepository->findOneBy(['id' => $id]);
        return $this->render('_application/components/_table.html.twig', [
            'table' => $table
        ]);
    }

    #[Route('/form_reload/{id}', name: 'app_application_form_reload')]
    public function formReload(
        int $id,
        FormRepository $formRepository,
        FormService $formService
    ): Response
    {
        $formEntity = $formRepository->findOneBy(['id' => $id]);
        $entity = new stdClass();
        $form = $formService->getEntityForm(
            $formEntity->getModule()->getSqlTable(),
            $entity,
            $formEntity
        );
        $formEntity->setHtml($form->createView());

        return $this->render('form/form.html.twig', [
            'form' => $formEntity
        ]);
    }

}
