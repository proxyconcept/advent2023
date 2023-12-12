<?php
$inputs = file('input.txt');

function count_matchs($win, $our) {
	$nb = 0;
	foreach ($our as $n) if (in_array($n, $win)) $nb++;
	return $nb;
}

$sum = 0;
$qty = array();
foreach ($inputs as $line) {
	if (! preg_match('/^Card *(\d+):([\d\s]+)\|([\d\s]+)$/', $line, $m)) die($line);
	$num = $m[1];
	$win = preg_split('/\s+/', trim($m[2]));
	$our = preg_split('/\s+/', trim($m[3]));
	
	$nb = count_matchs($win, $our);
	
	if (! isset($qty[ $num ])) $qty[ $num ] = 0;
	$qty[ $num ]++;
	
	for ($n = 1; $n <= $nb; $n++) {
		if (! isset($qty[ $num + $n ])) $qty[ $num + $n ] = 0;
		$qty[ $num + $n ]+= $qty[ $num ];
	}
	$sum+= $qty[ $num ];
}
printf("SUM=%d\n", $sum);
