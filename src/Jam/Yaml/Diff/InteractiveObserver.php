<?php

namespace Jam\Yaml\Diff;

use \Jam\Yaml\Diff\ResultWriter;
use \Jm_Console;

class InteractiveObserver implements ResultWriter
{

	protected $result;

	protected $console;

	public function __construct() {
		$this->console = Jm_Console::singleton();
	}


	public function init($fromfile, $tofile) {
	}

	public function add($key, $value) {
		$this->console->write("Add new key '$key'? [Y/n] >> ");
		if(in_array(
			strtolower($this->console->readln()),
			array('y', '', 'yes'),
			TRUE
		)) {
			$this->console->write("$key = [$value] >> ");
			$value = $this->console->readln();
		}
	}

	public function remove($key, $value) {
		$this->console->write("Remove key '$key'? (current value: $value) [Y/n] >> ");
		if(in_array(
			strtolower($this->console->readln()),
			array('y', '', 'yes'),
			TRUE
		)) {
			//
		}
	}

	public function change($key, $fromval, $toval) {
		$this->console->write("Change value of '$key' from '$fromval' to '$toval'? >> ");
		if(in_array(
			strtolower($this->console->readln()),
			array('y', '', 'yes'),
			TRUE
		)) {
			//
		}
	}
}
