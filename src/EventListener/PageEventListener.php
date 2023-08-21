<?php

namespace App\EventListener;

use App\Entity\Page;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Exception;

class PageEventListener
{

    public function prePersist(Page $page): void
    {
        if(!empty($page->getModule())){
            $page->setUri($page->getUri().'/{id}');
        }
    }


}