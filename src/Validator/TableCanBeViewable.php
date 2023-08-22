<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;


#[\Attribute]
class TableCanBeViewable extends Constraint
{

    public $message = 'No detailed page exist for this entity. A table row cannot be viewed';
    public string $mode = 'strict';

    public function getTargets(): array|string
    {
    	return self::CLASS_CONSTRAINT;
    }

}
