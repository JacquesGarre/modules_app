<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Field;
use App\Form\FieldType;
use Symfony\Component\HttpFoundation\Request;

class FieldController extends AbstractController
{
    #[Route('/administration/fields', name: 'app_fields')]
    public function index(): Response
    {
        return $this->render('fields/index.html.twig');
    }

    #[Route('/administration/fields/add', name: 'app_field_add')]
    public function add(Request $request): Response
    {        
        
        $field = new Field();
        $form = $this->createForm(
            FieldType::class, 
            $field,
            [
                'attr' => [
                    'data-form-name-value' => 'field',
                    'data-form-target' => 'form',
                    'data-controller' => 'form',
                    'data-form-url-value' => $this->generateUrl('app_field_add'),
                    'data-form-submit-label-value' => 'Submit'
                ]
            ]
        );

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                
                return $this->json([
                    'success' => 'blablabla'
                ]);

            } else {

                $errors = [];

                // Global
                foreach ($form->getErrors() as $error) {
                    $errors[$form->getName()][] = $error->getMessage();
                }
            
                // Fields
                foreach ($form as $child /** @var Form $child */) {
                    if (!$child->isValid()) {
                        foreach ($child->getErrors() as $error) {
                            $errors[$child->getName()][] = $error->getMessage();
                        }
                    }
                }
              
                return $this->json([
                    'errors' => $errors
                ]);

            }
        }

        // Render add form
        return $this->render('form_modal.html.twig',
            [   
                'title' => 'Create a new field',
                'form' => $form
            ]
        );
    }

}
