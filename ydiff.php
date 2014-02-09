<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'Jm/Autoloader.php';
$console = Jm_Console::singleton();

use Symfony\Component\Yaml\Parser;


class UdiffListener {

	public function __construct() {
		$this->console = Jm_Console::singleton();
	}


	public function addKeyToBeRemoved($key, $value) {
		$this->console->writeln("--- $key {$value}", 'red');
	}


	public function addKeyToBeAdded($key, $value) {
		$this->console->writeln("+++ $key = {$value}", 'cyan');
	}


	public function addKeyToBeChanged($key, $fromval, $toval) {
		$this->console->writeln("--- $key = {$fromval}", 'red');
		$this->console->writeln("+++ $key = {$toval}", 'cyan');
	}

}


$fromfile = $argv[1];
$tofile = $argv[2];

$parser = new Parser();
$from = $parser->parse(file_get_contents($fromfile));
$to   = $parser->parse(file_get_contents($tofile));

$console->writeln("--- {$fromfile}\t" . date('c', filemtime($fromfile)), 'green');
$console->writeln("+++ {$tofile}\t" . date('c', filemtime($tofile)), 'green');
# $console->writeln("@@ -1,* +1,* @@", 'bold');


function l($message) {
//	echo "$message" . PHP_EOL;
}	

function recdiff($from, $to, $listener, $index = '') {

	ksort($from);
	ksort($to);
	$keysfrom = array_keys($from);
	$keysto   = array_keys($to);

	while($key = array_shift($keysfrom)) {

		if(!in_array($key, $keysto)) {
			$listener->addKeyToBeRemoved($index . $key . ':', $from[$key]);
			continue;
		}

		l("key '$index:$key' was fround in both arrays");
		if(is_array($from[$key]) && is_array($to[$key])) {
			recdiff($from[$key], $to[$key], $listener, $index . $key . ':');
		} else if(is_array($from[$key])) {
			recdiff($from[$key], array(), $listener, $index . $key . ':');
		} else if(is_array($to[$key])) {
			recdiff(array(), $to[$key], $listener, $index . $key . ':');
		} else {
			// diff the values
			if($from[$key] != $to[$key]) {
				$listener->addKeyToBeChanged($index . $key . ':', $from[$key], $to[$key]);
			}
		}

		foreach($keysto as $i => $k) {
			if($k === $key) {
				unset($keysto[$i]);
			}
		}
	}

	// the remaining keys are only part of destination
	foreach($keysto as $key) {
		$listener->addKeyToBeAdded($index . $key . ':', $to[$key]);
		if(is_array($to[$key])) {
			recdiff(array(), $to[$key], $listener, $index . $key . ':');
		}
	}
}


$listener = new UdiffListener();
recdiff($from, $to, $listener);

