<?php

namespace Jam\Yaml\Diff;

interface ResultWriter
{

	function add($key, $value);

	function remove($key, $value);
	
	function change($key, $from, $to);
}

