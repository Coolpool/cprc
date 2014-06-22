<?php

class Tasks {

	public function sendMessage($message) {
		$this->sendXt('s', 'm#sm', $this->mNum, $this->id, $message);
	}

	public function sendSafeMessage($safeID) {
		$this->sendXt('s', 'u#ss', $this->mNum, $safeID);
	}

	public function sendEmote($emoteID) {
		$this->sendXt('s', 'u#se', $this->mNum, $emoteID);
	}

	public function sendPosition($x, $y) {
		$this->sendXt('s', 'u#sp', $this->mNum, $x, $y);
	}

	public function sendFrame($frameID) {
		$this->sendXt('s', 'u#sf', $this->mNum, $frameID);
	}

	public function joinRoom($roomID) {
		$this->sendXt('s', 'j#jr', $this->mNum, $roomID, 0, 0);
	}

	public function addStamp($stampID) {
		$this->sendXt('s', 'st#sse', $this->mNum, $stampID);
	}

	public function addItem($itemID) {
		$this->sendXt('s', 'i#ai', $this->mNum, $itemID);
	}

	public function addFurniture($furnID) {
		$this->sendXt('s', 'i#af', $this->mNum, $furniture);
	}

	public function throwSnowball($x, $y) {
		$this->sendPacket('s', 'u#sb', $this->mNum, $x, $y);
	}
}