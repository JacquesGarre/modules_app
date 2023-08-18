<?php

namespace App\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Entity\Table;
use App\Repository\FormRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class TableHasOneColumnValidator extends ConstraintValidator
{
    public function __construct(
        private RequestStack $requestStack,
        private FormRepository $formRepository
    )
    {
        $this->requestStack = $requestStack;
        $this->formRepository = $formRepository;
    }

    public function validate($table, Constraint $constraint): void
    {

    	if (!$constraint instanceof TableHasOneColumn) {
        	throw new UnexpectedTypeException($constraint, TableHasOneColumn::class);
        }
        
        if (!$table instanceof Table) {
        	throw new UnexpectedTypeException($table, Table::class);
        }

        $request = $this->requestStack->getCurrentRequest();
        if($request->isMethod('DELETE')){
            return;
        }
        
        if(count($table->getColumns()) < 1){
            $this->context->buildViolation($constraint->message)->addViolation();
        }
        
    }
}
