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

class FieldController extends AbstractController
{

    #[Route('/administration/fields', name: 'app_field_index')]
    public function index(FieldRepository $fieldRepository): Response
    {
        $fields = $fieldRepository->findAll();
        return $this->render('field/_table.html.twig', [
            'fields' => $fields,
        ]);
    }

    #[Route('/administration/fields/add/{moduleId}', name: 'app_field_add')]
    public function add(int $moduleId, ModalFormService $modal, Request $request, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['id' => $moduleId]);
        $field = new Field();
        $field->setModule($module);

        $params = [
            'moduleId' => $moduleId
        ];

        return $modal->show(
            $title = 'Create a new attribute for '.$module->getLabelSingular(),
            $class = 'field',
            $route = 'app_field_add',
            $request,
            $field,
            'POST',
            $params
        );
    }

    #[Route('/administration/fields/{id}/edit', name: 'app_field_edit')]
    public function edit(ModalFormService $modal, Request $request, int $id, FieldRepository $fieldRepository): Response
    {
        $field = $fieldRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];

        return $modal->show(
            $title = 'Edit attribute '.$field->getLabel(),
            $class = 'field',
            $route = 'app_field_edit',
            $request,
            $field,
            'POST',
            $params
        );
    }

    #[Route('/administration/fields/{id}/delete', name: 'app_field_delete')]
    public function delete(ModalFormService $modal, Request $request, int $id, FieldRepository $fieldRepository): Response
    {
        $field = $fieldRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];
        return $modal->show(
            $title = 'Delete attribute '.$field->getLabel(),
            $class = 'field',
            $route = 'app_field_delete',
            $request,
            $field,
            $method = 'DELETE',
            $params
        );
    }

}
