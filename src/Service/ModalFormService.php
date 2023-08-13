<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Exception;
use App\Service\FormService;
use Doctrine\ORM\Mapping\Entity;
use stdClass;

class ModalFormService
{

    public function __construct(
        private EntityManagerInterface $em, 
        private FormFactoryInterface $formFactory,
        private UrlGeneratorInterface $router,
        private Environment $twig,
        private FormService $formService
    )
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->twig = $twig;
        $this->formService = $formService; 
    }


    public function show(
        string $title,
        string $class,
        string $route,
        $request,
        $existingEntity = null, 
        $method = 'POST',
        $routeParams = [],
        $formEntity = null
    )
    {

        $entityClass = 'App\\Entity\\'.ucfirst($class);

        if(class_exists($entityClass)){
            $entity = $existingEntity ?: new $entityClass();
        } else {
            $entity = new stdClass();
            foreach($existingEntity as $key => $value){
                $entity->{$key} = $value;
            }
        }
        
        if(is_null($formEntity)){
            $form = $this->formService->getForm($class, $route, $entity, $method, $routeParams, 'write', true);

            

        } else {
            $form = $this->formService->getEntityForm($class, $entity, $formEntity);
        }


        if(empty($request->query->get('onchange'))){
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