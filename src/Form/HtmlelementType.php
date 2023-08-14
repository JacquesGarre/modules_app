<?php

namespace App\Form;

use App\Entity\HtmlElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use App\Entity\Table;
use App\Entity\Form;
use Symfony\Component\Validator\Constraints\NotNull;

class HtmlelementType extends AbstractType
{
    public function getDivElementForm($form)
    {
        $form
        ->add('type', ChoiceType::class, [
            'choices'  => [
                'Container' => 'container',
                'Row' => 'row',
                'Column' => 'col',
                'Table' => 'moduleTable',
                'Form' => 'moduleForm'
            ],
            'attr' => [
                'data-action' => 'change->form#onchange',
            ],
        ])
        ->add('sizeClass', ChoiceType::class, [
            'choices'  => [
                'Auto' => '',
                'Column' => [
                    'Medium 1' => '-md-1',
                    'Medium 2' => '-md-2',
                    'Medium 3' => '-md-3',
                    'Medium 4' => '-md-4',
                    'Medium 5' => '-md-5',
                    'Medium 6' => '-md-6',
                    'Medium 7' => '-md-7',
                    'Medium 8' => '-md-8',
                    'Medium 9' => '-md-9',
                    'Medium 10' => '-md-10',
                    'Medium 11' => '-md-11',
                    'Medium 12' => '-md-12',
                ],
                'Container' => [
                    'Fluid' => '-fluid',
                    'Small' => '-sm',
                    'Medium' => '-md',
                    'Large' => '-lg',
                    'Extra Large' => '-xl',
                    'Extra Extra Large' => '-xxl',
                ]
            ]
        ])
        ->add('additionnalClasses');
        // ->add('moduleTable', EntityType::class, [
        //     'placeholder' => '...',
        //     'label' => 'Table',
        //     'class'     => Table::class,
        //     'constraints' => [
        //         new NotBlank(),
        //         new NotNull()
        //     ]
        // ]);
        return $form;
    }

    private function getTableElementForm($form)
    {
        $form
        ->add('type', ChoiceType::class, [
            'choices'  => [
                'Container' => 'container',
                'Row' => 'row',
                'Column' => 'col',
                'Table' => 'moduleTable',
                'Form' => 'moduleForm'
            ],
            'attr' => [
                'data-action' => 'change->form#onchange',
            ],
        ])
        ->add('moduleTable', EntityType::class, [
            'placeholder' => '...',
            'label' => 'Table',
            'class'     => Table::class,
            'constraints' => [
                new NotBlank(),
                new NotNull()
            ]
        ])
        ->add('additionnalClasses');
        return $form;
    }

    private function getFormElementForm($form)
    {
        $form
        ->add('type', ChoiceType::class, [
            'choices'  => [
                'Container' => 'container',
                'Row' => 'row',
                'Column' => 'col',
                'Table' => 'moduleTable',
                'Form' => 'moduleForm'
            ],
            'attr' => [
                'data-action' => 'change->form#onchange',
            ],
        ])
        ->add('moduleForm', EntityType::class, [
            'label' => 'Form',
            'class'     => Form::class,
        ])
        ->add('moduleTable', EntityType::class, [
            'label' => 'Table',
            'class'     => Table::class,
            'constraints' => [
                new NotBlank(),
                new NotNull()
            ]
        ])
        ->add('additionnalClasses');
        return $form;
    }

    public function getForm($form, $htmlElement)
    {
        switch($htmlElement->getType()){
            case 'moduleTable':
                $form = $this->getTableElementForm($form);
            break;
            case 'moduleForm':
                $form = $this->getFormElementForm($form);
            break;
            case 'container':
            case 'row':
            case 'col':
            default:
                $form = $this->getDivElementForm($form);
            break;
        }

        $form->add('Submit', ButtonType::class, [
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
        $builder->add('type', ChoiceType::class, [
            'choices'  => [
                'Container' => 'container',
                'Row' => 'row',
                'Column' => 'col',
                'Table' => 'moduleTable',
                'Form' => 'moduleForm'
            ],
            'attr' => [
                'data-action' => 'change->form#onchange',
            ],
        ]);


        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {

                $htmlElement = $event->getData();

                dump($htmlElement);

                $form = $event->getForm();


                $method = $event->getForm()->getConfig()->getMethod();
                
                if($method !== 'DELETE'){

                    //$form = $this->getForm($form, $htmlElement);

  

                    if($htmlElement['type'] == 'moduleTable'){
                        $form->add('ModuleTable', EntityType::class, [
                            'label' => 'Table',
                            'class'     => Table::class,
    
                        ]);
                    }

                    $form->add('Submit', ButtonType::class, [
                        'attr' => [
                            'class' => 'btn-primary float-end',
                            'data-action' => 'form#submit',
                            'data-form-target' => 'submitBtn'
                        ],
                    ]);


                } else {
                    $form
                        ->add('sizeClass', HiddenType::class)
                        ->add('additionnalClasses', HiddenType::class)
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
            'data_class' => HtmlElement::class,
        ]);
    }
}
