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
use App\Entity\Module;
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

    private function getLists()
    {
        $elements = $this->listingRepository->getListIDS();
        $listIDS = array_map(function($element){
            return $element['list'];
        }, $elements);
        return ['...' => ''] + array_combine($listIDS, $listIDS);
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $field = $event->getData();
                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();

                if($method !== 'DELETE'){

                    $form
                    ->add('type', ChoiceType::class, [
                        'choices'  => [
                            'Text' => 'text',
                            'Listing' => 'listing',
                            'Many To One' => 'manytoone'
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
                    ->add('value', TextType::class, [
                        'label' => 'Default value',
                    ])
                    ->add('required')
                    ->add('disabled')
                    ->add('Submit', ButtonType::class, [
                        'attr' => [
                            'class' => 'btn-primary float-end',
                            'data-action' => 'form#submit',
                            'data-form-target' => 'submitBtn'
                        ],
                    ]);
                    
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


        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {

            $field = $event->getData();
            $form = $event->getForm();
            $method = $event->getForm()->getConfig()->getMethod();
            
            if($method !== 'DELETE'){
                foreach(array_keys($form->all()) as $fieldID){
                    $form->remove($fieldID);
                }
                

                switch($field['type']){
                    case 'text':
                        $form
                        ->add('type', ChoiceType::class, [
                            'choices'  => [
                                'Text' => 'text',
                                'Listing' => 'listing',
                                'Many To One' => 'manytoone'
                            ],
                            'constraints' => [
                                new NotBlank()
                            ],
                            'attr' => [
                                'data-action' => 'change->form#onchange',
                            ],
                        ])
                        ->add('label', TextType::class)
                        ->add('name', TextType::class)
                        ->add('value', TextType::class, [
                            'label' => 'Default value',
                        ])
                        ->add('required')
                        ->add('disabled')
                        ->add('Submit', ButtonType::class, [
                            'attr' => [
                                'class' => 'btn-primary float-end',
                                'data-action' => 'form#submit',
                                'data-form-target' => 'submitBtn'
                            ],
                        ]);
                    break;
                    case 'listing':
                        $form
                        ->add('type', ChoiceType::class, [
                            'choices'  => [
                                'Text' => 'text',
                                'Listing' => 'listing',
                                'Many To One' => 'manytoone'
                            ],
                            'constraints' => [
                                new NotBlank()
                            ],
                            'attr' => [
                                'data-action' => 'change->form#onchange',
                            ],
                        ])
                        ->add('label', TextType::class)
                        ->add('name', TextType::class)
                        ->add('list', ChoiceType::class, array(
                            'label' => 'List',
                            'choices' => $this->getLists(),
                        ))
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
                    break;
                    case 'manytoone':
                        $form
                        ->add('type', ChoiceType::class, [
                            'choices'  => [
                                'Text' => 'text',
                                'Listing' => 'listing',
                                'Many To One' => 'manytoone'
                            ],
                            'constraints' => [
                                new NotBlank()
                            ],
                            'attr' => [
                                'data-action' => 'change->form#onchange',
                            ],
                        ])
                        ->add('label', TextType::class)
                        ->add('name', TextType::class)
                        ->add('entity', EntityType::class, [
                            'label' => 'Entity',
                            'class' => Module::class
                        ])
                        ->add('required')
                        ->add('disabled')
                        ->add('Submit', ButtonType::class, [
                            'attr' => [
                                'class' => 'btn-primary float-end',
                                'data-action' => 'form#submit',
                                'data-form-target' => 'submitBtn'
                            ],
                        ]);
                    break;
                }

                $form->remove('Submit');
                $form->add('Submit', ButtonType::class, [
                    'attr' => [
                        'class' => 'btn-primary float-end',
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
