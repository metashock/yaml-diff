<?php

namespace Jam\Yaml\Diff;

use \ReflectionMethod;

class Result
{

	protected $writers;

	protected $fromfile;

	protected $tofile;



	public function __construct($fromfile, $tofile) {
		$this->writers = array();
		$this->fromfile = $fromfile;
		$this->tofile = $tofile;
	}


	public function attach($listener) {
		$this->writers []= $listener;
	}


	public function detach($listener) {
		$search = spl_object_hash($listener);
		foreach($this->writers as $key => $listener) {
			if(spl_object_hash($listener) === $search) {
				unset($this->writers[$key]);
			}
		}
	}

	public function listeners() {
		return $this->writers;
	}


	protected function trigger($event, $data){
		foreach($this->writers as $writer) {
			$method = new ReflectionMethod($writer, $event);
			$method->invokeArgs($writer, $data);		
		}
	}


	public function init() {
		$this->trigger('init', 
			array($this->fromfile(), $this->tofile()));
	}


	public function addKeyToBeRemoved($key, $value) {
		$this->trigger('remove', array($key, $value));
	}


	public function addKeyToBeAdded($key, $value) {
		$this->trigger('add', array($key, $value));
	}


	public function addKeyToBeChanged($key, $fromval, $toval) {
		$this->trigger('change', array($key, $fromval, $toval));
	}


	public function fromfile() {
		return $this->fromfile;
	}

	public function tofile() {
		return $this->tofile;
	}
}

