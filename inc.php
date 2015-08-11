<?php

function db() {
	$db = new PDO('sqlite:/tmp/routers.sqlite3');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
}

function p($str) {
	echo htmlspecialchars($str);
}

function format_time($time, $nicetime = false) {
	$text = '';
	if ($nicetime) { 
		$tc = array(60, 60, 24, 7, 4.35, 12); 
		$txt = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year'); 
		$left = abs($time); 
		for ($t = 0; $t < count($tc) && $left >= $tc[$t]; $t++) { 
			$left /= $tc[$t]; 
		} 
		$text = round($left)." ".$txt[$t].(round($left)!=1?"s":"").($time<0?" ago":" left"); 
	} else { 
		$tc = array(1, 60, 60, 24); 
		$res = array(0, 0, 0, 0); 
		$i=0; 
		for ($i = 0; $i < count($tc); $i++) { 
			$a = count($tc) - $i; 
			$p = array_product(array_slice($tc, 0, $a)); 
			while ($time >= $p) { 
				if (sizeof($res) >= $a) $res[$a-1]++; 
				$time -= $p; 
			} 
		} 
		$text = ''; 
		$text .= intval($res[3]).' days, '; 
		$text .= str_pad($res[2],2, '0', STR_PAD_LEFT).':'; 
		$text .= str_pad($res[1],2, '0', STR_PAD_LEFT).':';
		$text .= str_pad($res[0],2, '0', STR_PAD_LEFT);
	}
	return $text;
}

?>
