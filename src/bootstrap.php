<?php

require_once __DIR__ . '/silex.phar';

$loader->register();

$app = new Silex\Application();
$app[ 'autoloader' ]->registerNamespace('Symfony', __DIR__ . '/../vendor');
$app[ 'autoloader' ]->registerNamespace('EduterCNERTA', __DIR__ . '/../vendor');
$app[ 'autoloader' ]->registerNamespace('Twig', __DIR__ . '/../vendor');



$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
));

$app['twig']->addExtension(new \EduterCNERTA\TwigExtentions\MyTwigExtension());

return $app;