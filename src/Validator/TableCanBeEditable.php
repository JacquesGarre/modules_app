<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;


#[\Attribute]
class TableCanBeEditable extends Constraint
{

    public $message = 'No edition form exist for this entity. Table cannot be editable';
    public string $mode = 'strict';

    public function getTargets(): array|string
    {
    	return self::CLASS_CONSTRAINT;
    }

}
