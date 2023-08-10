<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;


use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class FieldController extends AbstractController
{
    
    #[Route('/administration/fields', name: 'app_fields')]
    public function index(): Response
    {
        return $this->render('fields/index.html.twig');
    }

    #[Route('/administration/fields/add', name: 'app_field_add')]
    public function add(ModalFormService $modal, Request $request): Response
    {        
        return $modal->show(
            $title = 'Create a new field',
            $class = 'field',
            $route = 'app_field_add',
            $request
        );
    }

}
