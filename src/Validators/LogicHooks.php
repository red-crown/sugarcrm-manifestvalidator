<?php

namespace Fbsg\ManifestValidator\Validators;


use SplFileInfo;
use Symfony\Component\Finder\Finder;

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
     * @var \Symfony\Component\Finder\Finder;
     */
    private $finder;

    function __construct(array $defs)
    {
        parent::__construct($defs);

        $this->finder = new Finder();
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

            if ($hookClass = $this->checkClass($hook)) {
                $this->validateClassMethods($hookClass, $hook);
            } else {
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
     * @param string $hookFile
     * @param array  $hook
     *
     * @return bool
     */
    private function validateClassMethods($hookFile, array $hook)
    {
        $contents = file_get_contents($hookFile);
        $contents = preg_replace('/require.+?;|\<\?php/', '', $contents);

        try {
            eval($contents);

            if (!method_exists($hook['class'], $hook['function'])) {
                $this->errors[] = "Method {$hook['function']} doesn't exist in {$hook['class']}";

                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();

            return false;
        }
    }

    /**
     * @param array $hook
     *
     * @return bool
     */
    private function checkClass(array $hook)
    {
        $file     = new SplFileInfo($hook['file']);
        $filename = $file->getBasename();

        $iterator = $this->finder
            ->files()
            ->name($filename)
            ->in($this->rootDir);

        $found = [];

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $found[] = $file->getRealPath();
            }
        }

        if (count($found) > 0) {
            /** @var SplFileInfo $class */
            return $found[0];
        } else {
            $this->missingClasses[] = $filename;
            return false;
        }
    }
}