<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;


#[\Attribute]
class TableHasOneColumn extends Constraint
{

    public $message = 'Please add at least one column to your table';
    public string $mode = 'strict';

    public function getTargets(): array|string
    {
    	return self::CLASS_CONSTRAINT;
    }

}
