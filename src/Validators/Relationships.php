<?php

namespace Fbsg\ManifestValidator\Validators;


class Relationships extends Validator
{
    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->defs as $index => $def) {
            if (empty($def['meta_data'])) {
                $this->errors[] = "Missing meta_data key for relationships at $index";
            }

            $file = new \SplFileInfo($this->replaceBasepath($def['meta_data']));

            if (!$file->isFile()) {
                $this->errors[] = "The file '{$def['meta_data']}' can not be found.";
            }
        }

        return $this->passes();
    }
}
