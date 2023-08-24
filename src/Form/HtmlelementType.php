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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Listing;
use App\Repository\ListingRepository;
use App\Repository\ModuleRepository;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use App\Entity\Table;
use App\Entity\Form;
use App\Entity\Page;
use App\Repository\PageRepository;
use Symfony\Component\Validator\Constraints\NotNull;

class HtmlelementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {

            $htmlElement = $event->getData();
            $form = $event->getForm();
            $method = $event->getForm()->getConfig()->getMethod();
            
            if($method !== 'DELETE'){


                switch($htmlElement->getLayoutPart()){
                    case 'SidebarMenu':
                        $form->add('type', HiddenType::class, [
                            'data' => 'SidebarMenu',
                        ]);
                        $form->add('layoutPart', HiddenType::class);
                        $form->add('content', TextType::class);
                        $form->add('pagelink', EntityType::class, [
                            'class' => Page::class,
                            'query_builder' => function (PageRepository $repository) {
                                return $repository
                                    ->createQueryBuilder('p')
                                    ->where('p.uri NOT LIKE :placeholder')
                                    ->setParameter('placeholder', '%/{id}%');
                            },
                        ]);
                    break;
                    case 'SidebarHeader':
                        $form->add('layoutPart', HiddenType::class);
                        $form->add('type', ChoiceType::class, [
                            'choices'  => [
                                'H1' => 'h1',
                                'H2' => 'h2',
                                'H3' => 'h3',
                                'H4' => 'h4',
                                'H5' => 'h5',
                                'H6' => 'h6'
                            ],
                            'attr' => [
                                'data-action' => 'change->form#onchange',
                            ],
                        ]);
                        $form->add('content', TextType::class);
                    break;
                    default:
                        $form->add('type', ChoiceType::class, [
                            'choices'  => [
                                'Container' => 'container',
                                'Row' => 'row',
                                'Column' => 'col',
                                'Table' => 'moduleTable',
                                'Form' => 'moduleForm',
                                'H1' => 'h1',
                                'H2' => 'h2',
                                'H3' => 'h3',
                                'H4' => 'h4',
                                'H5' => 'h5',
                                'H6' => 'h6'
                            ],
                            'attr' => [
                                'data-action' => 'change->form#onchange',
                            ],
                        ]);
                        $form->add('sizeClass', ChoiceType::class, [
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
                        ]);
                    break;
                }


                $form->add('additionnalClasses');
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


        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {

            $htmlElement = $event->getData();
            $form = $event->getForm();
            $method = $event->getForm()->getConfig()->getMethod();
            
            if($method !== 'DELETE'){

                switch($htmlElement['type']){
                    case 'moduleTable':
                        $form->remove('moduleForm');
                        $form->add('moduleForm', EntityType::class, [
                            'label' => 'Form',
                            'class' => Form::class,
                            'row_attr' => [
                                'class' => 'd-none'
                            ]
                        ]);
                        $form->remove('sizeClass');
                        $form->add('sizeClass', HiddenType::class);
                        $form->remove('additionnalClasses');
                        $form->add('additionnalClasses', HiddenType::class);
                        $form->add('moduleTable', EntityType::class, [
                            'label' => 'Table',
                            'class'     => Table::class,
                        ]);
                    break;
                    case 'moduleForm':
                        $form->remove('moduleTable');
                        $form->add('moduleTable', EntityType::class, [
                            'label' => 'Table',
                            'class'     => Table::class,
                            'row_attr' => [
                                'class' => 'd-none'
                            ]
                        ]);
                        $form->remove('sizeClass');
                        $form->add('sizeClass', HiddenType::class);
                        $form->remove('additionnalClasses');
                        $form->add('additionnalClasses', HiddenType::class);
                        $form->add('moduleForm', EntityType::class, [
                            'label' => 'Form',
                            'class'     => Form::class,
                        ]);
                    break;
                    case 'SidebarMenu':
    
                        $form->remove('moduleTable');
                        $form->remove('moduleForm');
                        $form->remove('sizeClass');
                        $form->remove('additionnalClasses');
                        $form->remove('type');
                        $form->remove('content');
                        $form->remove('pagelink');
                        $form->remove('layoutPart');
                        $form->add('sizeClass', HiddenType::class);
                        $form->add('additionnalClasses', HiddenType::class);
                        $form->add('layoutPart', HiddenType::class);
                        $form->add('type', HiddenType::class);
                        $form->add('content', TextType::class);
                        $form->add('pagelink', EntityType::class, [
                            'class' => Page::class,
                            'query_builder' => function (PageRepository $repository) {
                                return $repository
                                    ->createQueryBuilder('p')
                                    ->where('p.uri NOT LIKE :placeholder')
                                    ->setParameter('placeholder', '%/{id}%');
                            },
                        ]);
                    break;
                    case 'h1':
                    case 'h2':
                    case 'h3':
                    case 'h4':
                    case 'h5':
                    case 'h6':
                        $form->remove('moduleTable');
                        $form->remove('moduleForm');
                        $form->remove('sizeClass');
                        $form->remove('additionnalClasses');
                        $form->remove('type');
                        $form->remove('content');
                        $form->add('layoutPart', HiddenType::class);
                        $form->add('type', ChoiceType::class, [
                            'choices'  => [
                                'H1' => 'h1',
                                'H2' => 'h2',
                                'H3' => 'h3',
                                'H4' => 'h4',
                                'H5' => 'h5',
                                'H6' => 'h6'
                            ],
                            'attr' => [
                                'data-action' => 'change->form#onchange',
                            ],
                        ]);
                        $form->add('content', TextType::class);
                        $form->add('additionnalClasses');
                    break;
                    case 'row':
                    case 'col':
                    case 'container':
                    default:
                        $form->remove('sizeClass');
                        $form->remove('additionnalClasses');
                        $form->add('sizeClass', ChoiceType::class, [
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
                        ]);
                        $form->add('additionnalClasses');
                        $form->remove('moduleTable');
                        $form->add('moduleTable', EntityType::class, [
                            'label' => 'Table',
                            'class'     => Table::class,
                            'row_attr' => [
                                'class' => 'd-none'
                            ]
                        ]);
                        $form->remove('moduleForm');
                        $form->add('moduleForm', EntityType::class, [
                            'label' => 'Form',
                            'class'     => Form::class,
                            'row_attr' => [
                                'class' => 'd-none'
                            ]
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
            'data_class' => HtmlElement::class,
        ]);
    }
}
