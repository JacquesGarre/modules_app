<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;

class FieldController extends AbstractController
{

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
