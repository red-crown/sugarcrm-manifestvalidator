<?php

namespace Fbsg\ManifestValidator\Exceptions;

use Exception;

/**
 * Class ValidationException
 *
 * @package Fbsg\ManifestValidator\Exceptions
 */
class ValidationException extends Exception
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @param array $errors
     */
    function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct(implode("\n", $errors));
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}