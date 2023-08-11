<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ModalFormService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ListingRepository;

class ListingController extends AbstractController
{
    #[Route('/administration/listings/add', name: 'app_listing_add')]
    public function add(ModalFormService $modal, Request $request): Response
    {
        return $modal->show(
            $title = 'Create a new listing',
            $class = 'listing',
            $route = 'app_listing_add',
            $request
        );
    }

    #[Route('/administration/listings/{id}/edit', name: 'app_listing_edit')]
    public function edit(ModalFormService $modal, Request $request, int $id, ListingRepository $listingRepository): Response
    {   

        $listing = $listingRepository->findOneBy(['id' => $id]);
        $params = [];
        $params['id'] = $listing->getId();
        return $modal->show(
            $title = 'Edit element '.$listing->getLabel(),
            $class = 'listing',
            $route = 'app_listing_edit',
            $request,
            $listing,
            'POST',
            $params
        );
    }

    #[Route('/administration/listings/{id}/delete', name: 'app_listing_delete')]
    public function delete(ModalFormService $modal, Request $request, int $id, ListingRepository $listingRepository): Response
    {
        $listing = $listingRepository->findOneBy(['id' => $id]);
        $params = [];
        $params['id'] = $listing->getId();
        return $modal->show(
            $title = 'Delete element '.$listing->getLabel(),
            $class = 'listing',
            $route = 'app_listing_delete',
            $request,
            $listing,
            $method = 'DELETE',
            $params
        );
    }

    #[Route('/administration/listings/{id}', name: 'app_listing_show')]
    public function show(
        int $id,
        ModalFormService $modal,
        Request $request,
        ListingRepository $listingRepository
    ): Response
    {
        $listing = $listingRepository->findOneBy(['id' => $id]);
        $params = [];
        $params['id'] = $listing->getId();
        $form = $modal->getForm('listing', 'app_listing_show', $listing, 'POST', $params);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            return $modal->handlePostRequest($form, $listing);
        }

        return $this->render('listing/show.html.twig', [
            'listing' => $listing,
            'form' => $form
        ]);
    }
}
