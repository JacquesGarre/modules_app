<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use App\Entity\Module;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FormService
{
    public function __construct(
        private EntityManagerInterface $em, 
        private FormFactoryInterface $formFactory,
        private UrlGeneratorInterface $router,
        private Environment $twig
    )
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->twig = $twig;
    }
    
    public function getForm(string $class, string $route, $entity, $method, $routeParams = [], $mode = 'write', $keepMode = false){
        $typeClass = 'App\\Form\\' . ucfirst($class).'Type'::class;

        if(!class_exists($typeClass)){
            $form = $this->formFactory->createNamed(
                $class, 
                FormType::class,
                $entity,
                [   
                    'action' => $this->router->generate($route, $routeParams),
                    'method' => 'POST',
                    'attr' => [
                        'data-form-keep-mode-value' => $keepMode,
                        'data-mode' => $mode,
                        'data-form-method-value' => 'post',
                        'data-form-name-value' => $class,
                        'data-form-target' => 'form',
                        'data-controller' => 'form',
                        'data-form-url-value' => $this->router->generate($route, $routeParams),
                        'data-form-submit-label-value' => 'Submit',
                        'data-form-table-value' => $class
                    ]
                ]
            );
            $form->add(
                'Submit', 
                ButtonType::class,
                [
                    'label' => 'Submit',
                    'attr' => [
                        'class' => 'btn-danger float-end',
                        'data-action' => 'form#submit',
                        'data-form-target' => 'submitBtn'
                    ]
                ]
            );
            return $form;
        }




        return $this->formFactory->create(
            $typeClass, 
            $entity,
            [   
                'method' => $method,
                'attr' => [
                    'data-form-keep-mode-value' => $keepMode,
                    'data-mode' => $mode,
                    'data-form-method-value' => 'post',
                    'data-form-name-value' => $class,
                    'data-form-target' => 'form',
                    'data-controller' => 'form',
                    'data-form-action-value' => '',
                    'data-form-url-value' => $this->router->generate($route, $routeParams),
                    'data-form-submit-label-value' => 'Save',
                    'data-form-table-value' => $class
                ]
            ]
        );

    }


    public function getEntityForm(
        string $class, 
        $entity, 
        $formEntity,
        $mode = 'write'
    )
    {


        if($formEntity->getAction() == 'add' || empty($entity->id)){
            $action = $this->router->generate('app_data_add', [
                'moduleId' => $formEntity->getModule()->getId()
            ]);
        } elseif($formEntity->getAction() == 'edit'){
            $action = $this->router->generate('app_data_edit', [
                'moduleId' => $formEntity->getModule()->getId(),
                'id' => $entity->id
            ]);
        }

        $form = $this->formFactory->createNamed(
            $class, 
            FormType::class,
            $entity,
            [   
                'action' => $action,
                'method' => 'POST',
                'attr' => [
                    'data-form-keep-mode-value' => false,
                    'data-mode' => $mode,
                    'data-form-method-value' => 'post',
                    'data-form-name-value' => $class,
                    'data-form-action-value' => $formEntity->getAction(),
                    'data-form-target' => 'form',
                    'data-controller' => 'form',
                    'data-form-url-value' => $this->router->generate('app_application_form_reload', [
                        'id' => $formEntity->getId()
                    ]), 
                    'data-form-submit-label-value' => 'Submit',
                    'data-form-table-value' => $class
                ]
            ]
        );

        foreach($formEntity->getFields() as $field){

            switch($field->getType()){
                case 'text':
                    
                    // default value
                    if(!isset($entity->{$field->getName()})){
                        $entity->{$field->getName()} = $field->getValue() ?? null;
                    }

                    $form->add($field->getName(), TextType::class, [
                        'label' => $field->getLabel(),
                        'data' => $entity->{$field->getName()},
                        'disabled' => $mode == 'read' ? $mode : $field->isDisabled(),
                        'required' => $field->isRequired(),
                    ]);
                break;
                case 'listing':
                  
                    // default value
                    if(!isset($entity->{$field->getName()})){

                        if($field->isMultiple()){
                            $entity->{$field->getName()} = !is_array($field->getValue()) ? [$field->getValue()] : [];
                        } else {
                            $entity->{$field->getName()} = $field->getValue() ?? null;
                        }

                    } else {

                        if($field->isMultiple() && !is_array($entity->{$field->getName()})){
                            $entity->{$field->getName()} = json_decode($entity->{$field->getName()});
                        }

                    }

                    $form->add($field->getName(), ChoiceType::class, [
                        'label' => $field->getLabel(),
                        'data' => $entity->{$field->getName()},
                        'disabled' => $mode == 'read' ? $mode : $field->isDisabled(),
                        'required' => $field->isRequired(),
                        'choices' => $field->getChoices(),
                        'multiple'  => $field->isMultiple(),
                    ]);
                    
                break;
                case 'manytoone':

                    // default value
                    if(!isset($entity->{$field->getName()})){                        
                        $entity->{$field->getName()} = $field->getValue() ?? null;
                    }

                    $form->add($field->getName(), ChoiceType::class, [
                        'label' => $field->getLabel(),
                        'data' => $entity->{$field->getName()},
                        'disabled' => $mode == 'read' ? $mode : $field->isDisabled(),
                        'required' => $field->isRequired(),
                        'choices' => $field->getChoices(),
                    ]);
                break;
                case 'manytomany':

                    // default value
                    if(!isset($entity->{$field->getName()})){                        
                        $entity->{$field->getName()} = $field->getValue() ?? null;
                    }

                    $form->add($field->getName(), ChoiceType::class, [
                        'label' => $field->getLabel(),
                        'data' => is_array($entity->{$field->getName()}) ? $entity->{$field->getName()} : json_decode($entity->{$field->getName()}),
                        'disabled' => $mode == 'read' ? $mode : $field->isDisabled(),
                        'required' => $field->isRequired(),
                        'choices' => $field->getChoices(),
                        'multiple'  => true,
                    ]);
                break;
            }
        }
        switch($formEntity->getAction()){
            case 'add':
                $form->add(
                    'Submit', 
                    ButtonType::class,
                    [
                        'label' => 'Create '.$formEntity->getModule()->getLabelSingular(),
                        'attr' => [
                            'class' => 'btn-primary float-end',
                            'data-action' => 'form#submit',
                            'data-form-target' => 'submitBtn'
                        ]
                    ]
                );
            break;
            case 'edit':
                $label = $mode == 'read'  ? 'Edit' : 'Update';
                $action = $mode == 'read'  ? 'form#enable' : 'form#submit';
                $form->add(
                    $label, 
                    ButtonType::class, 
                    [
                        'label' => $label,
                        'attr' => [
                            'class' => 'btn-primary float-end',
                            'data-action' => $action,
                            'data-form-target' => 'submitBtn'
                        ]
                    ]
                );
            break;
        }

        return $form;
    }



}