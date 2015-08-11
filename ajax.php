<?php

require_once('inc.php');

header('Cache-Control: no-cache, must-revalidate');
function subscription() {
	$data = file_get_contents('http://link.halon.se/sr/request/subscriptions/?s='.$_GET['serial']);
        if ($data == '')
		die(json_encode(array('error' => 'connection failed')));
	$subs = json_decode($data, true);
	foreach ($subs as &$t) {
		if ($t > 0)
			$t = format_time($t, true);
		else
			$t = 'Expired';
	}
	die(json_encode($subs));
}
function version_uptime() {
	$q = db()->prepare('SELECT url,username,password FROM units WHERE id=:id');
	if (!$q->execute(array(':id' => $_GET['id'])))
		die('SELECT failed');
	$r = $q->fetch();
	$client = new SoapClient($r['url'].'/?wsdl',array(
            'location' => $r['url'],
            'uri' => 'urn:halon',
            'login' => $r['username'],
            'password' => $r['password'] 
            ));
	$res['version'] = $client->getVersion()->result;
	$res['uptime'] = format_time($client->getUptime()->result);
	die(json_encode($res));

}

if ($_GET['type'] == 'subscription')
	subscription();
if ($_GET['type'] == 'versionuptime')
	version_uptime();

?>
