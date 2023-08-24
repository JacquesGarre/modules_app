<?php

namespace App\Form;

use App\Entity\Page;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Layout;


class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options): void {

                $page = $event->getData();

                $form = $event->getForm();

                $method = $event->getForm()->getConfig()->getMethod();

                if($method !== 'DELETE'){
                    $form
                        ->add('title', TextType::class, [
                            'disabled' => $options['attr']['data-mode'] == 'read'   
                        ]);
                    
                    if($page->getModule()){
                        $form->add('uri', TextType::class, [
                            'disabled' => $options['attr']['data-mode'] == 'read',
                        ]);
                    } else {
                        $form->add('uri', TextType::class, [
                            'disabled' => $options['attr']['data-mode'] == 'read'   
                        ]);
                    }

                    $form->add('pageLayout', EntityType::class, [
                        'class'         => Layout::class,
                        'disabled' => $options['attr']['data-mode'] == 'read',
                        'placeholder' => '...',
                    ]);


                    $label = $options['attr']['data-mode'] == 'read'  ? 'Edit' : 'Submit';
                    $action = $options['attr']['data-mode'] == 'read'  ? 'form#enable' : 'form#submit';
                    $form
                        ->add($label, ButtonType::class, [
                            'attr' => [
                                'class' => 'btn-primary float-end',
                                'data-action' => $action,
                                'data-form-target' => 'submitBtn'
                            ],
                        ]);
                } else {
                    $form
                        ->add('title', HiddenType::class)
                        ->add('uri', HiddenType::class);
                    $form
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
            'data_class' => Page::class,
            "allow_extra_fields" => true
        ]);
    }
}
