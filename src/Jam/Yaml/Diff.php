<?php

namespace Jam\Yaml;

use Jam\Yaml\Diff\Result;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;


class Diff
{

	protected $result;

	protected $parser;


	public function __construct($fromfile, $tofile) {
		$this->parser = new Parser();
		$this->result = new Result($fromfile, $tofile);
	}

	public function parser() {
		return $this->parser;
	}

	public function result() {
		return $this->result;
	}

	public function exec() {
		$from = $this->parser->parse(file_get_contents($this->result()->fromfile()));
		$to   = $this->parser->parse(file_get_contents($this->result()->tofile()));
		$this->result()->init();
		$this->compareDictionaries($from, $to, $this->result());
		$dumper = new Dumper();
		echo $dumper->dump($from, 4, 4);	
	}


	/**
	 *
	 *
	 */
	protected function compareDictionaries($from, $to, $result, $index = '') {

		ksort($from);
		ksort($to);
		$keysfrom = array_keys($from);
		$keysto   = array_keys($to);

		while($key = array_shift($keysfrom)) {

			if(!in_array($key, $keysto)) {
				$result->addKeyToBeRemoved($index . $key . ':', $from[$key]);
				continue;
			}

			if(is_array($from[$key]) && is_array($to[$key])) {
				$this->compareDictionaries($from[$key], $to[$key], $result, $index . $key . ':');
			} else if(is_array($from[$key])) {
				$this->compareDictionaries($from[$key], array(), $result, $index . $key . ':');
			} else if(is_array($to[$key])) {
				$this->compareDictionaries(array(), $to[$key], $result, $index . $key . ':');
			} else {
				// diff the values
				if($from[$key] != $to[$key]) {
					$result->addKeyToBeChanged($index . $key . ':', $from[$key], $to[$key]);
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
			$result->addKeyToBeAdded($index . $key . ':', $to[$key]);
			if(is_array($to[$key])) {
				$this->compareDictionaries(array(), $to[$key], $result, $index . $key . ':');
			}
		}
	}
}

