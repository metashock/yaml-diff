<?php

namespace Jam\Yaml\Diff;

use \Jam\Yaml\Diff\ResultWriter;
use \Jm_Console;

class ConsoleWriter implements ResultWriter
{

	protected $result;

	protected $console;

	public function __construct() {
		$this->console = Jm_Console::singleton();
	}


	public function init($fromfile, $tofile) {
		$this->console->writeln("--- $fromfile\t" . date('c', filemtime($fromfile)), 'green');
		$this->console->writeln("+++ $tofile\t" . date('c', filemtime($tofile)), 'green');
		$this->console->writeln("@@ -1,* +1,* @@", 'bold');
	}

	public function add($key, $value) {
		$this->console->writeln("+++ $key = {$value}", 'cyan');
	}

	public function remove($key, $value) {
		$this->console->writeln("--- $key {$value}", 'red');
	}

	public function change($key, $fromval, $toval) {
		$this->console->writeln("--- $key = {$fromval}", 'red');
		$this->console->writeln("+++ $key = {$toval}", 'cyan');
	}
}

