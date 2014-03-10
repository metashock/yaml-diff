<?php

require_once '../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem(array('../tpl'));
$twig = new Twig_Environment($loader);

echo $twig->render('index.twig', array('name' => 'Thorsten'));


