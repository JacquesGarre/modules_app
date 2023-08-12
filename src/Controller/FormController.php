<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FormRepository;
use App\Repository\ModuleRepository;
use App\Entity\Form;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class FormController extends AbstractController
{
    #[Route('/administration/forms/add/{moduleId}', name: 'app_form_add')]
    public function add(int $moduleId, ModalFormService $modal, Request $request, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->findOneBy(['id' => $moduleId]);
        $form = new Form();
        $form->setModule($module);

        $params = [
            'moduleId' => $moduleId
        ];

        return $modal->show(
            $title = 'Create a new form for '.$module->getLabelSingular(),
            $class = 'form',
            $route = 'app_form_add',
            $request,
            $form,
            'POST',
            $params
        );
    }

    #[Route('/administration/forms/{id}/edit', name: 'app_form_edit')]
    public function edit(ModalFormService $modal, Request $request, int $id, FormRepository $formRepository): Response
    {
        $form = $formRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];

        return $modal->show(
            $title = 'Edit attribute '.$form->getTitle(),
            $class = 'form',
            $route = 'app_form_edit',
            $request,
            $form,
            'POST',
            $params
        );
    }

    #[Route('/administration/forms/{id}/delete', name: 'app_form_delete')]
    public function delete(ModalFormService $modal, Request $request, int $id, FormRepository $formRepository): Response
    {
        $form = $formRepository->findOneBy(['id' => $id]);
        $params = ['id' => $id];
        return $modal->show(
            $title = 'Delete attribute '.$form->getTitle(),
            $class = 'form',
            $route = 'app_form_delete',
            $request,
            $form,
            $method = 'DELETE',
            $params
        );
    }

    #[Route('/administration/forms/{id}', name: 'app_form_show')]
    public function show(
        int $id,
        FormRepository $formRepository
    ): Response
    {
        $formEntity = $formRepository->findOneBy(['id' => $id]);
        $entity = new stdClass();
        $form = $this->getForm($formEntity, $entity);
        return $this->render('form/show.html.twig', [
            'formEntity' => $formEntity,
            'form' => $form
        ]);
    }


    public function getForm(Form $formEntity, stdClass $entity)
    {
        $form = $this->createFormBuilder($entity, [
            'action' => '/'.$formEntity->getAction(),
            'method' => 'POST',
        ]);
        foreach($formEntity->getFields() as $field){
            $entity->{$field->getName()} = null;
            switch($field->getType()){
                case 'text':
                    $form->add($field->getName(), TextType::class, [
                        'empty_data' => $field->getValue(),
                        'disabled' => $field->isDisabled(),
                        'required' => $field->isRequired(),
                    ]);
                break;
                case 'listing':
                    $form->add($field->getName(), ChoiceType::class, [
                        'empty_data' => $field->getValue(),
                        'disabled' => $field->isDisabled(),
                        'required' => $field->isRequired(),
                        'choices' => ['...' => ''] + $field->getChoices(),
                    ]);
                break;
            }
        }
        switch($formEntity->getAction()){
            case 'add':
                $form->add('save', ButtonType::class, ['label' => 'Create '.$formEntity->getModule()->getLabelSingular()]);
            break;
            case 'edit':
                $form->add('save', ButtonType::class, ['label' => 'Update '.$formEntity->getModule()->getLabelSingular()]);
            break;
        }
        return $form->getForm();
    }

}
