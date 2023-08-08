<?php

use Illuminate\Filesystem;
use Illuminate\Validation;
use Illuminate\Translation;

// Service factory for the ORM
$container['validator'] = function ($c) {

    $filesystem = new Filesystem\Filesystem();
    $fileLoader = new Translation\FileLoader($filesystem, '');
    $translator = new Translation\Translator($fileLoader, 'en_US');
    $factory = new Validation\Factory($translator);

    return $factory;
};
