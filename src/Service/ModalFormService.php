<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
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
        $request
    )
    {
        $entityClass = 'App\\Entity\\'.ucfirst($class);
        $entity = new $entityClass();

        $form = $this->getForm($class, $route, $entity);

        // If form has been submitted
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            return $this->handlePostRequest($form, $entity);
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

    private function getForm(string $class, string $route, $entity){

        $typeClass = 'App\\Form\\' . ucfirst($class).'Type'::class;
        return $this->formFactory->create(
            $typeClass, 
            $entity,
            [
                'attr' => [
                    'data-form-name-value' => $class,
                    'data-form-target' => 'form',
                    'data-controller' => 'form',
                    'data-form-url-value' => $this->router->generate($route),
                    'data-form-submit-label-value' => 'Save'
                ]
            ]
        );
    }

    private function handlePostRequest($form, $entity){

        
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
            if (!$child->isValid()) {
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