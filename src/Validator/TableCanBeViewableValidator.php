<?php

namespace App\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Entity\Table;
use App\Repository\FormRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class TableCanBeViewableValidator extends ConstraintValidator
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

    	if (!$constraint instanceof TableCanBeViewable) {
        	throw new UnexpectedTypeException($constraint, TableCanBeViewable::class);
        }
        
        if (!$table instanceof Table) {
        	throw new UnexpectedTypeException($table, Table::class);
        }

        $request = $this->requestStack->getCurrentRequest();
        if($request->isMethod('DELETE')){
            return;
        }
        
        if(in_array('view', $table->getInlineActions())){
            $hasPage = $table->getModule()->getPage();
            if(empty($hasPage)){
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
        
    }
}
