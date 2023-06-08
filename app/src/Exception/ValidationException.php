<?php
namespace App\Exception;

use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationException
 */
class ValidationException extends RuntimeException
{
    private ConstraintViolationListInterface $violations;

    /**
     * @param ConstraintViolationListInterface $violations
     */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;

        parent::__construct('Validation failed.');
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        $errors = [];

        foreach ($this->violations as $constraint) {
            $errors[$constraint->getPropertyPath()][] = $constraint->getMessage();
        }

        return $errors;
    }
}
