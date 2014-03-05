<?php

namespace Fbsg\ManifestValidator\Validators;


class Beans extends Validator
{
    /**
     * @var array
     */
    private $requiredKeys = [
        'module',
        'class',
        'path',
    ];

    /**
     * @var array
     */
    private $missingKeys = [];

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->defs as $index => $def) {
            if (!$this->checkRequiredKeys($def)) {
                $this->errors[] = "Missing the required keys " . implode(",", $this->missingKeys). " at bean[{$index}]";
            }

            if (!$this->checkClassName($def)) {
                $this->errors[] = "The class name does not match the filename for the bean '{$def['module']}'";
            }
        }

        return $this->passes();
    }

    /**
     * @param $def
     *
     * @return bool
     */
    private function checkClassName($def)
    {
        $filename  = pathinfo($def['path'], PATHINFO_FILENAME);

        return $def['class'] == $filename;
    }

    /**
     * @param $def
     *
     * @return bool
     */
    private function checkRequiredKeys($def)
    {
        $this->missingKeys = array_diff_key(array_flip($this->requiredKeys), $def);

        return empty($this->missingKeys);
    }
}