<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Exception;
use App\Entity\Module;

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
}