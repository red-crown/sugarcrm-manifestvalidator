<?php

use Fbsg\ManifestValidator\Exceptions\ValidationException;
use Fbsg\ManifestValidator\ManifestValidatorService;

require_once __DIR__ . '/../vendor/autoload.php';

$path = '/Users/matthew/FAYBUS/oldhic/ProductionModule/src';

$v = new ManifestValidatorService($path);

try {
    $v->validate();
    echo "IS VALID" . PHP_EOL;
} catch (ValidationException $e) {
    echo $e . PHP_EOL;
} catch (\Exception $e) {
    echo $e->getMessage();
}
