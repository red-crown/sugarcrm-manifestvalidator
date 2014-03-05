<?php

namespace Fbsg\ManifestValidator\Validators;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;

/**
 * Class LogicHooks
 *
 * @package Fbsg\ManifestValidator\Validators
 */
class LogicHooks extends Validator
{
    /**
     * @var array
     */
    private $requiredKeys = [
        'module',
        'hook',
        'order',
        'description',
        'file',
        'class',
        'function',
    ];

    /**
     * @var array
     */
    private $missingClasses = [];

    /**
     * @var array
     */
    private $missingKeys = [];

    /**
     * @var array
     */
    private $projectFiles = [];


    /**
     * @param array $defs
     */
    function __construct(array $defs)
    {
        parent::__construct($defs);
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->defs as $index => $hook) {

            if (!$this->checkKeys($hook)) {
                $this->errors[] = "Missing the keys " . implode(",", $this->missingKeys) . " at index $index";
            }

            if (!$this->checkClass($hook) and !empty($this->missingClasses)) {
                $this->errors[] = "Missing LogicHook class " . implode(",", $this->missingClasses) . " at index $index";
            }

        }

        return $this->passes();
    }

    /**
     * @param array $hook
     *
     * @return bool
     */
    private function checkKeys(array $hook)
    {
        $this->missingKeys = array_keys(array_diff_key(array_flip($this->requiredKeys), $hook));

        return empty($this->missingKeys);
    }

    /**
     * @return array
     */
    private function loadProjectFiles()
    {
        if (empty($this->projectFiles)) {
            $directory     = new RecursiveDirectoryIterator($this->rootDir);
            $iterator      = new RecursiveIteratorIterator($directory);
            $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::MATCH);

            foreach ($regexIterator as $file) {
                /** @var SplFileInfo $file */
                $this->projectFiles[] = $file;
            }
        }

        return $this->projectFiles;
    }

    /**
     * @param array $hook
     *
     * @return bool
     */
    private function checkClass(array $hook)
    {
        $classFile = new SplFileInfo($hook['file']);
        $fileNames = array_map(
            function ($file) {
                /** @var SplFileInfo $file */
                return $file->getBasename();
            },
            $this->loadProjectFiles()
        );

        if (!array_key_exists($classFile->getBasename(), array_flip($fileNames))) {
            $this->errors[] = "Couldn't find the file for the class {$hook['class']}";

            return false;
        }

        $matchedFiles = array_filter(
            $this->loadProjectFiles(),
            function ($file) use($classFile) {
                /** @var SplFileInfo $file */
                return $classFile->getBasename() == $file->getBasename();
            }
        );

        // If we find a matched class file, and it contains the proper
        // method for the logic hook, we return true. Otherwise, we know
        // that the logic hook is invalid.
        foreach ($matchedFiles as $file) {
            /** @var SplFileInfo $file */
            if ($this->inspectClass($file->getRealPath(), $hook)) {

                return true;
            }
        }

        $this->errors[] = "The class {$hook['class']} in file: {$hook['file']} ".
                          "doesn't appear to contain the method {$hook['function']}";

        return false;
    }

    /**
     * @param string $hookFile
     * @param array  $hook
     *
     * @return bool
     */
    private function inspectClass($hookFile, array $hook)
    {
        $contents   = file_get_contents($hookFile);
        $className  = $hook['class'];
        $methodName = $hook['function'];
        $errors     = [];

        $count = preg_match('/class\s+?([\w]+?)(?:\s|$)/', $contents, $matches);
        if ($count < 1 or $matches[$count] != $className) {
            $errors[] = "The specified logic hook class \"{$className}\" could not be found in $hookFile";
        }

        $count = preg_match('/function\s+?('.$methodName.')\s?+\(/', $contents, $matches);
        if ($count < 1) {
            $errors[] = "The specified method '{$methodName}' could not be found in $hookFile";
        }

        if (!empty($errors)) {
            //$this->errors = array_merge($this->errors, $errors);

            return false;
        }

        return true;
    }

}