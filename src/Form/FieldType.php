<?php

namespace App\Form;

use App\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Listing;
use App\Repository\ListingRepository;
use App\Repository\ModuleRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FieldType extends AbstractType
{
    public function __construct(
        private ListingRepository $listingRepository,
        private ModuleRepository $moduleRepository,
        private UrlGeneratorInterface $router
    )
    {
        $this->listingRepository = $listingRepository;
        $this->moduleRepository = $moduleRepository;
        $this->router = $router;
    }

    private function getTables()
    {
        $elements = $this->moduleRepository->getTables();
        $listIDS = array_map(function($element){
            return $element['sqlTable'];
        }, $elements);
        return ['...' => ''] + array_combine($listIDS, $listIDS);
    }

    private function getLists()
    {
        $elements = $this->listingRepository->getListIDS();
        $listIDS = array_map(function($element){
            return $element['list'];
        }, $elements);
        return ['...' => ''] + array_combine($listIDS, $listIDS);
    }

    public function getForm($form, $field){

        switch($field->getType()){
            case 'listing':
                $form = $this->getListingFieldForm($form);
            break;
            case 'text':
            default:
                $form = $this->getTextFieldForm($form);
            break;
        }


        return $form;

    }

    private function getListingFieldForm($form){
        
        $form
        ->add('type', ChoiceType::class, [
            'choices'  => [
                'Text' => 'text',
                'Listing' => 'listing',
            ],
            'constraints' => [
                new NotBlank()
            ],
            'attr' => [
                'data-action' => 'change->form#onchange',
            ],
        ])
        ->add('label', TextType::class, [
            'constraints' => [
                new NotBlank()
            ],
        ])
        ->add('name', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Regex('/^[A-Za-z0-9_]*$/')
            ],
        ])
        ->add('list', ChoiceType::class, array(
            'label' => 'List',
            'choices' => $this->getLists(),
        ))
        ->add('value', TextType::class, [
            'label' => 'Default value',
        ])
        ->add('multiple')
        ->add('required')
        ->add('disabled')
        ->add('Submit', ButtonType::class, [
            'attr' => [
                'class' => 'btn-primary float-end',
                'data-action' => 'form#submit',
                'data-form-target' => 'submitBtn'
            ],
        ]);
        return $form;
    }

    private function getTextFieldForm($form){
        
        $form
        ->add('type', ChoiceType::class, [
            'choices'  => [
                'Text' => 'text',
                'Listing' => 'listing',
            ],
            'constraints' => [
                new NotBlank()
            ],
            'attr' => [
                'data-action' => 'change->form#onchange',
            ],
        ])
        ->add('label', TextType::class, [
            'constraints' => [
                new NotBlank()
            ],
        ])
        ->add('name', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Regex('/^[A-Za-z0-9_]*$/')
            ],
        ])
        ->add('list', HiddenType::class)
        ->add('value', TextType::class, [
            'label' => 'Default value',
        ])
        ->add('multiple', HiddenType::class)
        ->add('required')
        ->add('disabled')
        ->add('Submit', ButtonType::class, [
            'attr' => [
                'class' => 'btn-primary float-end',
                'data-action' => 'form#submit',
                'data-form-target' => 'submitBtn'
            ],
        ]);
        return $form;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $field = $event->getData();
                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();

                if($method !== 'DELETE'){

                    $form = $this->getForm($form, $field);
                    
                } else {

                    $form
                    ->add('type', HiddenType::class)
                    ->add('label', HiddenType::class)
                    ->add('name', HiddenType::class)
                    ->add('value', HiddenType::class)
                    ->add('Submit', ButtonType::class, [
                        'attr' => [
                            'class' => 'btn-danger float-end',
                            'data-action' => 'form#submit',
                            'data-form-target' => 'submitBtn'
                        ],
                    ]);
                    
                }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Field::class,
            "allow_extra_fields" => true
        ]);
    }
}
