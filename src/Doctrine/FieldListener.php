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
        private DataService $dataService
    )
    {
        $this->dataService = $dataService;
        $this->listingRepository = $listingRepository;
    }


    public function postLoad(Field $fieldEntity)
    {
        // Set list to field if field type == listing
        if($fieldEntity->getType() == 'listing'){

            $list = $fieldEntity->getList();
            $choices = $this->listingRepository->findBy(['list' => $list]);
            $choicesID = array_map(function($listing){
                return $listing->getValue();
            }, $choices);
            $choicesLabels = array_map(function($listing){
                return $listing->getLabel();
            }, $choices);
            $fieldEntity->setChoices(['...' => ''] + array_combine($choicesLabels, $choicesID));

            $options = [];
            foreach($choices as $choice){
                $options[$choice->getValue()] = [
                    'value' => $choice->getValue(),
                    'label' => $choice->getLabel(),
                    'colorClass' => $choice->getColorClass(),
                    'bgClass' => $choice->getBgClass()
                ];
            }
            $fieldEntity->setSelectOptions($options);
        }

        // Set list to field if field type == manytoone
        if($fieldEntity->getType() == 'manytoone' || $fieldEntity->getType() == 'manytomany' ){

            $externalModule = $fieldEntity->getEntity();
            $items = $this->dataService->get($externalModule->getSqlTable());

            $choicesID = array_map(function($item){
                return $item['id'];
            }, $items);
            $choicesLabels = array_map(function($item){
                return $item['titlePattern'];
            }, $items);

            $fieldEntity->setChoices(['...' => ''] + array_combine($choicesLabels, $choicesID));

            $options = [];
            foreach($items as $item){
                $url = !empty($externalModule->getPage()) ? '/'.str_replace('/{id}', '/'.$item['id'], $externalModule->getPage()->getUri()) : false;
                $options[$item['id']] = [
                    'value' => $item['id'],
                    'label' => $item['titlePattern'],
                    'colorClass' => '',
                    'bgClass' => '',
                    'detailedViewPath' => $url
                ];
            }
            $fieldEntity->setSelectOptions($options);
        }
        

    }
}
