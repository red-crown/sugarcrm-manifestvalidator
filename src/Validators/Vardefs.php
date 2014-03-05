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
        return $to->getBasename() == $to->getBasename(".php");
    }
}