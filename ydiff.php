<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'Jm/Autoloader.php';
Jm_Autoloader::singleton()->prependPath('src');
$console = Jm_Console::singleton();

use Symfony\Component\Yaml\Parser;
use Jam\Yaml\Diff;
use Jam\Yaml\Diff\ConsoleWriter;
use Jam\Yaml\Diff\InteractiveObserver;

$fromfile = $argv[1];
$tofile = $argv[2];

$diff = new Diff($fromfile, $tofile);
# $diff->result()->attach(new ConsoleWriter());
$diff->result()->attach(new InteractiveObserver());
$diff->exec($fromfile, $tofile);

