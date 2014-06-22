<?php

trait Crypto {

	public function swapMD5($md5) {
		return substr($md5, 16, 16) . substr($md5, 0, 16);
	}

	public function encryptPassword($password) {
		return md5($password);
	}

	public function generateKey($password, $rndKey) {
		$key = $this->swapMD5($this->encryptPassword(strtoupper($password)));
		$key .= $rndKey;
		$key .= 'a1ebe00441f5aecb185d0ec178ca2305Y(02.>\'H}t":E1_root';
		return $this->swapMD5($this->encryptPassword($key));
	}
}

?>
