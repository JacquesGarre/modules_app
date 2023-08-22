<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;


#[\Attribute]
class TableCanHaveAddAction extends Constraint
{

    public $message = 'No add form exist for this entity. Table cannot have add action';
    public string $mode = 'strict';

    public function getTargets(): array|string
    {
    	return self::CLASS_CONSTRAINT;
    }

}
