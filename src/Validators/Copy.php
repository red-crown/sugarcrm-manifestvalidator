<?php

namespace Fbsg\ManifestValidator\Validators;

use SplFileInfo;


/**
 * Class Copy
 *
 * @package Fbsg\ManifestValidator\Validators
 */
class Copy extends Validator
{
    /**
     * @var array
     */
    protected static $messages = [
        'ERR_FROM' => "File not found.",
        'ERR_TO'   => "The \"To\" and \"From\" filenames do not match.",
    ];

    /**
     * @var string
     */
    protected $keyTo = 'to';

    /**
     * @var string
     */
    protected $keyFrom = 'from';

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->defs as $index => $copy) {

            if (!array_key_exists($this->keyFrom, $copy) or !array_key_exists($this->keyTo, $copy)) {
                if (!array_key_exists($this->keyFrom, $copy)) {
                    $this->errors[] = 'Missing key ' . $this->keyFrom . " at index {$index} ";
                }

                if (!array_key_exists($this->keyTo, $copy)) {
                    $this->errors[] = 'Missing key ' . $this->keyTo . " at index {$index} ";
                }

                continue;
            }

            $from = new SplFileInfo($this->replaceBasepath($copy[$this->keyFrom]));
            $to   = new SplFileInfo($copy[$this->keyTo]);

            if (!$this->checkCopyFrom($from)) {
                $this->errors[] = static::$messages['ERR_FROM'] . ' ' . $this->replaceBasepath($copy[$this->keyFrom]);
            }

            if (!$this->checkCopyTo($to, $from)) {
                $message        = " From: {$from->getBasename()} To: {$to->getBasename()}";
                $this->errors[] = static::$messages['ERR_TO'] . $message;
            }
        }

        return $this->passes();
    }

    /**
     * @param SplFileInfo $from
     *
     * @return bool
     */
    protected function checkCopyFrom(SplFileInfo $from)
    {
        return $from->isFile() or $from->isDir();
    }

    /**
     * @param SplFileInfo $to
     * @param SplFileInfo $from
     *
     * @return bool
     */
    protected function checkCopyTo(SplFileInfo $to, SplFileInfo $from)
    {
        return $to->getBasename() === $from->getBasename();
    }
}