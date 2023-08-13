<?php

namespace App\Doctrine;

use App\Entity\Field;
use App\Entity\Listing;
use App\Repository\ListingRepository;
use App\Service\DataService;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class FieldListener
{
    public function __construct(
        private ListingRepository $listingRepository,
    )
    {
        $this->listingRepository = $listingRepository;
    }


    public function postLoad(Field $fieldEntity)
    {
        // Set list to field is field type == listing
        if($fieldEntity->getType() == 'listing'){
            $list = $fieldEntity->getList();
            $choices = $this->listingRepository->findBy(['list' => $list]);
            $choicesID = array_map(function($listing){
                return $listing->getId();
            }, $choices);
            $choicesLabels = array_map(function($listing){
                return $listing->getLabel();
            }, $choices);
            $fieldEntity->setChoices(['...' => ''] + array_combine($choicesLabels, $choicesID));
        }


    }
}
