<?php

namespace Fbsg\ManifestValidator\Validators;


use SplFileInfo;

/**
 * Class Vardefs
 *
 * @package Fbsg\ManifestValidator\Validators
 */
class Vardefs extends Copy
{
    /**
     * @var string
     */
    protected $keyTo = 'to_module';

    /**
     * @param SplFileInfo $to
     *
     * @return bool
     */
    protected function checkCopyTo(SplFileInfo $to)
    {
        return substr($to, 0, 1) == strtoupper(substr($to, 0, 1));
    }
}