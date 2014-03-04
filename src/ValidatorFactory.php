<?php

namespace Fbsg\ManifestValidator;


/**
 * Class ValidatorFactory
 *
 * @package Fbsg\ManifestValidator
 */
class ValidatorFactory
{

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @param string $rootDir
     */
    function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $defName
     * @param array  $def
     *
     * @return Validators\Validator;
     */
    public function make($defName, array $def)
    {
        switch ($defName) {
            case 'copy':
                $classname = 'Copy';
                break;

            case 'language':
                $classname = 'Language';
                break;

            case 'vardefs':
                $classname = 'Vardefs';
                break;

            case 'logic_hooks':
                $classname = 'LogicHooks';
                break;

            /*
            case 'relationships':
                $classname = 'Relationships';
                break;

            case 'beans':
                $classname = 'Beans';
                break;

            case 'layoutdefs':
                $classname = 'Layoutdefs';
                break;

            case 'image_dir':
                $classname = 'Imagedir';
                break;

            case 'layoutfields':
                $classname = 'Layoutfields';
                break;
            //*/

            default:
                $classname = 'Generic';
                break;
        }

        $namespace = '\Fbsg\ManifestValidator\Validators\\';
        $class     = $namespace . $classname;

        /** @var Validators\Validator $validator */
        $validator = new $class($def);
        $validator->setRootDir($this->rootDir);

        return $validator;
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }
}