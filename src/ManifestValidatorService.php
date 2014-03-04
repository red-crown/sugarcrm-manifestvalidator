<?php

namespace Fbsg\ManifestValidator;


/**
 * Class ManifestValidatorService
 *
 * @package Fbsg\ManifestValidator
 */
class ManifestValidatorService
{
    const MANIFEST_FILENAME = 'manifest.php';

    /**
     * @var string
     */
    protected $packageDir;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $manifest;

    /**
     * @var array
     */
    protected $installdefs;

    /**
     * @var \Fbsg\ManifestValidator\ValidatorFactory
     */
    protected $factory;

    /**
     * @param string $packageDir
     *
     * @throws \Exception
     */
    function __construct($packageDir)
    {
        $this->packageDir = $packageDir;

        if (!is_dir($this->packageDir)) {
            throw new \Exception("The directory $packageDir does not exist.");
        }

        $this->loadManifest();

        $this->factory = new ValidatorFactory($this->packageDir);
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->installdefs as $defName => $def) {

            if (!is_array($def)) {
                continue;
            }

            $validation = $this->factory->make($defName, $def);
            $validation->validate();

            if ($validation->fails()) {
                $this->errors = array_merge($this->errors, $validation->getErrors());
            }
        }

        if (!empty($this->errors)) {
            throw new Exceptions\ValidationException($this->errors);
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @throws \Exception
     */
    protected function loadManifest()
    {
        /** @noinspection PhpIncludeInspection */
        require $this->packageDir . '/' . self::MANIFEST_FILENAME;

        if (empty($manifest)) {
            throw new \Exception("No manifest definition was found!");
        }

        if (empty($installdefs)) {
            throw new \Exception("No installdefs definition was found!");
        }

        $this->manifest    = $manifest;
        $this->installdefs = $installdefs;
    }
}