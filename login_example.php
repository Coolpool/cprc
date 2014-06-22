<?php

include 'include.php';

$penguin  = new ClubPenguin();
$console  = new Console();
$username = $console->getInput("Username: ");
$password = $console->getInput("Password: ");

# Connect
$penguin->connect($username, $password, "204.75.167.22", 3724);
$console->fine("Connected user " . $username);

# Join Room
$penguin->joinRoom(100);
$console->fine("Joined room Town");

# Send Message "Hello"
$penguin->sendMessage("Hello");
$console->fine("Sent message 'Hello'");
$penguin->sleep(3);

# Disconnect
$penguin->disconnect();
exit();

?>
