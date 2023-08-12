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

class FieldType extends AbstractType
{
    public function __construct(
        private ListingRepository $listingRepository,
        private ModuleRepository $moduleRepository
    )
    {
        $this->listingRepository = $listingRepository;
        $this->moduleRepository = $moduleRepository;
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

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $field = $event->getData();
                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();
                
                if($method !== 'DELETE'){

                    $form
                        ->add('type', ChoiceType::class, [
                            'choices'  => [
                                'Text' => 'text',
                                'Listing' => 'listing',
                                // 'Table' => 'foreignTable'
                            ],
                            'constraints' => [
                                new NotBlank()
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
                        ->add('foreignTable', ChoiceType::class, array(
                            'label' => 'Table',
                            'choices' => $this->getTables()
                        ))
                        ->add('list', ChoiceType::class, array(
                            'label' => 'List',
                            'mapped' => false,
                            'choices' => $this->getLists()
                        ))
                        ->add('listings', EntityType::class, array(
                            'label' => 'Elements',
                            'class'     => Listing::class,
                            'expanded'  => true,
                            'multiple'  => true,
                        ))

                        ->add('value')
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Field::class,
        ]);
    }
}
