<?php

require __DIR__ . "/vendor/autoload.php";


$app = new Lib\Container;

$tool = new Lib\Train;
$app->bind('Lib\ToolInterface', $tool);
// $app->bind('Lib\ToolInterface', 'Lib\train');
$app->bind('visitor', 'Lib\Visitor');


$visitor = $app->make('visitor');
$visitor->go();
