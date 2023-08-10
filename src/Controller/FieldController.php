<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FieldController extends AbstractController
{
    #[Route('/administration/fields', name: 'app_fields')]
    public function index(): Response
    {
        return $this->render('fields/index.html.twig');
    }

    #[Route('/administration/fields/add', name: 'app_field_add')]
    public function add(): Response
    {
        // Save in db
        if(!empty($_POST)){

            // Form validation
            if(empty($_POST['label'])){
                return $this->json([
                    'error' => 'Label cannot be empty'
                ]);
            }


            return $this->json([
                'success' => 'blablabla'
            ]);
            
        }

        // Render add form
        return $this->render('fields/add.html.twig');
    }

}
