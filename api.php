<?php

require_once('inc.php');

header('Content-Type: text/plain');

if ($_GET['api-key'] != 'secret')
	die('invalid api-key');

if ($_GET['type'] == 'unit') {
	$q = db()->prepare('SELECT * FROM units WHERE serial = :serial');
	if (!$q->execute(array(':serial' => $_GET['serial'])))
		die('SELECT failed');

	// insert new system
	if (!$q->fetch()) {
		$q = db()->prepare('INSERT INTO units (serial,seen) VALUES (:serial,DATETIME(\'now\'))');
		if (!$q->execute(array(':serial' => $_GET['serial'])))
			die('INSERT failed');
		die('ok');
	}

	// update "seen"
	$q = db()->prepare('UPDATE units SET seen = DATETIME(\'now\') WHERE serial = :serial');
	if (!$q->execute(array(':serial' => $_GET['serial'])))
		die('UPDATE failed');
	die('ok');
}
