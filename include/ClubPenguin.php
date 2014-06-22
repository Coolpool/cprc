<?php

require_once "Tasks.php";
require_once "Crypto.php";

class ClubPenguin extends Tasks {

	use Crypto;

	private $isConnected = false;
	private $socket;
	public $id;
	public $mNum;

	public function __call($method_name, $args) {
		include_once 'include/Console.php';
		$console = new Console();
		$console->{$method_name}($args[0]);
	}

	public function getPort($username) {
		$ascii = 0;
		$split = str_split($username);
		foreach($split as $k)
			$ascii += ord($k);
		if($ascii % 2 == 0)
			return 3724;
		else
			return 6112;
	}

	public function getLogin() {
		$ips = array('204.75.167.218', '204.75.167.219', '204.75.167.176', '204.75.167.177');
		return array_rand($ips);
	}

	public function sendPacket($packet) {
		$packet .= chr(0);
		$length = strlen($packet);
		return socket_send($this->socket, $packet, $length, 0);
		while(true)
			$this->fine("Packet sent: " . $packet);
	}

	public function sendXt() {
		$arrPacket = func_get_args();
		$strPacket = "%xt%";
		$strPacket .= implode("%", $arrPacket) . "%";
		$this->sendPacket($strPacket);
	}

	public function recv() {
		$recv = socket_recv($this->socket, $data, 8192, 0);
		return $data;
	}

	public function decodePacket($raw, $needle) {
		$decodedPacket = array();
		$split 		   = explode($needle, $raw);
		foreach($split as $k)
			$decodedPacket[] = $k;
		return $decodedPacket;
	}

	public function stribet($data, $left, $right) {
		$pl = stripos($data, $left) + strlen($left);
	    $pr = stripos($data, $left, $pl);
	    return substr($data, $pl, $pr - $pl);
	}

	public function search($data, $needle) {
		$pos = strpos($data, $needle);
		if($pos === true)
			return true;
		else
			return false;
	}

	public function sendAndWait($socket, $data, $needle) {
		$length = strlen($data);
		socket_send($socket, $data, $length, 0);
		$buffer = socket_recv($socket, $data, 1024);
		return $buffer;
	}

	public function disconnect() {
		$this->fatal("Shutting down!");
		socket_close($socket);
	}

	public function sleep($seconds) {
		$this->info("Sleeping for " . $seconds . " seconds...");
		sleep($seconds);
	}

	public function connect($username, $password, $server, $port) {
		$this->info("Attempting to connect. ");
		$this->info("Connection: " . var_dump(func_get_args()));
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_connect($socket, $this->getLogin(), $port);
		$this->sendAndWait($socket, "<policy-file-request/>", "<cross-domain-policy/>");
		$this->sendAndWait($socket, "<msg t='sys'><body action='verChk' r='0'><ver v='153' /></body></msg>", "apiOK");
		$rndKey  = $this->stribet($this->sendAndWait($socket, "<msg t='sys'><body action='rndK' r='-1'></body></msg>", "rndK"), "<k>", "</k>");
		$key     = $this->generateKey($password, $rndKey);
		$rawData = $this->sendAndWait("<msg t='sys'><body action='login' r='0'><login z='w1'><nick><![CDATA[" . $username ."]]></nick><pword><![CDATA[" . $key . "]]></pword></login></body></msg>", "%xt%l%-1");
		
		if($this->search($rawData, "%xt%l%-1")) {
			$this->isConnected = true;
			$this->great("Connected " . $username . " to Club Penguin!");
		} else {
			$this->fatal("Failed to connect " . $username . " to Club Penguin! Try again? (y/n)");
			$input = strtolower($this->getInput());
			if($input == "y") {
				$this->info("OK. Trying again...");
				$this->connect($username, $password, $server, $port);
			} elseif($input == "n") {
				$this->info("OK. Goodbye.");
				exit();
			} else {
				$this->bad("I don't know what " . $input . " means, so i'm gonna leave now. Bye.");
				exit();
			}
		}

		$packet    	     = $this->decodePacket($rawData, "%");
		$new_split 	     = $this->decodePacket($packet[4], "|");
		$this->id  	     = $new_split[0];
		$raw       		 = $packet[4];
		$confirmationKey = $packet[5];
		$loginKey 		 = $new_split[3];
		socket_close($socket);
		$this->info("Joining server " . $server);
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_connect($this->socket, $server, $port);
		$this->sendAndWait($this->socket, "<policy-file-request/>", "</cross-domain-policy");
		$this->sendAndWait($this->socket, "<msg t='sys'><body action='verChk' r='0'><ver v='153' /></body></msg>");
		$rndK = $this->stribet($this->sendAndWait($this->socket, "<msg t='sys'><body action='rndK'r='-1'></body></msg>", "rndK"), "<k>", "</k>");
		$key2 = $this->swapMD5($this->encryptPassword($loginKey . $rndK) . $loginKey);
		$this->sendAndWait($this->socket, "<msg t='sys'><body action='login' r='0'><login z='w1'><nick><![CDATA[" . $raw . "]]></nick><pword><![CDATA[" . $key2 . "#" . $confirmationKey . "]]></pword></login></body></msg>", "%xt%l%-1%");
		$this->sendAndWait($this->socket, "%xt%s%j#js%-1%" . $this->id . "%");
		$loginData   = $this->sendAndWait($this->socket, "%xt%s%g#gi%-1%", "gi");
		$split_again = $this->decodePacket($loginData, "%");
		$this->mNum  = $split_again[44];
		$this->fine($username . " joined server " . $server);
	}
}

?>
