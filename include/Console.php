<?php

class Console {

	public function __call($method_name, $args) {
		$prefix = strtoupper($method_name);
		$msg    = $args[0];
		if(isset($args[1])) $break  = $args[1];
		$output = "[" . $prefix . "] > " . $msg;
		if(isset($break) && $break == false)
			echo $output;
		else
			echo $output . chr(10);
	}
	
	public function getInput($msg) {
		$this->input($msg, false);
		return trim(fgets(STDIN));
	}

}

?>
