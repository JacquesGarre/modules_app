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
        $form = $this->formFactory->create(
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
                    'data-form-url-value' => $this->router->generate($route, $routeParams),
                    'data-form-submit-label-value' => 'Save',
                    'data-form-table-value' => $class
                ]
            ]
        );
        return $form;
    }


    public function getEntityForm(
        string $class, 
        $entity, 
        $formEntity
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
                    'data-mode' => 'write',
                    'data-form-method-value' => 'post',
                    'data-form-name-value' => $class,
                    'data-form-target' => 'form',
                    'data-controller' => 'form',
                    'data-form-url-value' => $action,
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
                        'data' => $entity->{$field->getName()},
                        'disabled' => $field->isDisabled(),
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

                    }

                    $form->add($field->getName(), ChoiceType::class, [
                        'data' => $entity->{$field->getName()},
                        'disabled' => $field->isDisabled(),
                        'required' => $field->isRequired(),
                        'choices' => $field->getChoices(),
                        'multiple'  => $field->isMultiple(),
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
                            'class' => 'btn-success float-end',
                            'data-action' => 'form#submit',
                            'data-form-target' => 'submitBtn'
                        ]
                    ]
                );
            break;
            case 'edit':
                $form->add(
                    'Submit', 
                    ButtonType::class, 
                    [
                        'label' => 'Update '.$formEntity->getModule()->getLabelSingular()
                    ]
                );
            break;
        }

        return $form;
    }



}