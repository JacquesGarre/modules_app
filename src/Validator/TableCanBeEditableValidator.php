<?php

namespace App\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Entity\Table;
use App\Repository\FormRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class TableCanBeEditableValidator extends ConstraintValidator
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

    	if (!$constraint instanceof TableCanBeEditable) {
        	throw new UnexpectedTypeException($constraint, TableCanBeEditable::class);
        }
        
        if (!$table instanceof Table) {
        	throw new UnexpectedTypeException($table, Table::class);
        }

        $request = $this->requestStack->getCurrentRequest();
        if($request->isMethod('DELETE')){
            return;
        }
        
        if(in_array('edit', $table->getInlineActions())){
            $hasEditForm = false;
            foreach($table->getModule()->getForms() as $form){
                if($form->getAction() == 'edit'){
                    $hasEditForm = true;
                    break;
                }
            }
            if(!$hasEditForm){
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
        
    }
}
