<?php

namespace Fbsg\ManifestValidator\Validators;

use SplFileInfo;

/**
 * Class Language
 *
 * @package Fbsg\ManifestValidator\Validators
 */
class Language extends Copy
{
    /**
     * @var array
     */
    protected static $messages = [
        'ERR_FROM' => 'File not found.',
        'ERR_TO'   => 'Not a valid module.',
    ];

    /**
     * @var string
     */
    protected $keyTo = 'to_module';

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->defs as $index => $copy) {
            if (!array_key_exists('language', $copy)) {
                $this->errors[] = "Missing language key in languages[{$index}]";
            }
        }

        return parent::validate();
    }

    /**
     * @param SplFileInfo $from
     *
     * @return bool
     */
    protected function checkCopyFrom(SplFileInfo $from)
    {
        if (substr($from->getBasename(), 0, 5) == 'en_us') {
            return parent::checkCopyFrom($from);
        } else {
            return false;
        }
    }

    /**
     * @param SplFileInfo $to
     *
     * @return bool
     */
    protected function checkCopyTo(SplFileInfo $to)
    {
        $basename = $to->getBasename();

        return substr($basename, 0, 1) == strtoupper(substr($basename, 0, 1));
    }
}