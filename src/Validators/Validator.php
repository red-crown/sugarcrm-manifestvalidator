<?php

namespace Fbsg\ManifestValidator\Validators;


/**
 * Class Validator
 *
 * @package Fbsg\ManifestValidator\Validators
 */
abstract class Validator
{
    const BASEPATH_STRING = "<basepath>";

    /**
     * @var array
     */
    protected $defs;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param array $defs
     */
    function __construct(array $defs)
    {
        $this->defs = $defs;
    }

    /**
     * @return bool
     */
    public abstract function validate();

    /**
     * @return bool
     */
    public function passes()
    {
        return empty($this->errors);
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function replaceBasepath($string)
    {
        return str_replace(self::BASEPATH_STRING, $this->rootDir, $string);
    }

    /**
     * @param $rootDir
     *
     * @return $this
     */
    final public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;

        return $this;
    }
}