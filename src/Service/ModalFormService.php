<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Exception;

class ModalFormService
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


    public function show(
        string $title,
        string $class,
        string $route,
        $request,
        $existingEntity = null, 
        $method = 'POST',
        $routeParams = []
    )
    {
        $entityClass = 'App\\Entity\\'.ucfirst($class);
        $entity = $existingEntity ?: new $entityClass();

        $form = $this->getForm($class, $route, $entity, $method, $routeParams);

        // If creation/update
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            return $this->handlePostRequest($form, $entity);
        }

        // If delete
        if ($request->isMethod('DELETE')) {
            $form->handleRequest($request);
            return $this->handleDeleteRequest($form, $entity);
        }
        

        // Render add form
        $html = $this->twig->render('form_modal.html.twig',
            [   
                'title' => $title,
                'form' => $form->createView()
            ]
        );
        return new Response($html);
    }

    public function getForm(string $class, string $route, $entity, $method, $routeParams = []){
        $typeClass = 'App\\Form\\' . ucfirst($class).'Type'::class;
        $form = $this->formFactory->create(
            $typeClass, 
            $entity,
            [   
                'method' => $method,
                'attr' => [
                    'data-form-method-value' => 'post',
                    'data-form-name-value' => $class,
                    'data-form-target' => 'form',
                    'data-controller' => 'form',
                    'data-form-url-value' => $this->router->generate($route, $routeParams),
                    'data-form-submit-label-value' => 'Save'
                ]
            ]
        );


        return $form;
    }

    private function handleDeleteRequest($form, $entity){

        $errors = [];

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->em->remove($entity);
                $this->em->flush();
            } catch(Exception $e) {
                $errors[$form->getName()][] = $e->getMessage();
                return new Response(json_encode([
                    'errors' => $errors
                ]));
            }

            return new Response(json_encode([
                'success' => 'Entity deleted'
            ]));

        } 

        // Global
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }
    
        // Fields
        foreach ($form as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }


        return new Response(json_encode([
            'errors' => $errors
        ]));

    }

    public function handlePostRequest($form, $entity){

        
        $errors = [];

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->em->persist($entity);
                $this->em->flush();
            } catch(Exception $e) {
                $errors[$form->getName()][] = $e->getMessage();
                return new Response(json_encode([
                    'errors' => $errors
                ]));
            }

            return new Response(json_encode([
                'success' => $entity->getId()
            ]));
        } 

        // Global
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }
    
        // Fields
        foreach ($form as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }
        return new Response(json_encode([
            'errors' => $errors
        ]));

    }

}