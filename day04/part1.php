<?php
$inputs = file('input.txt');

function count_points($win, $our) {
	$nb = 0;
	foreach ($our as $n) if (in_array($n, $win)) $nb++;
	return ($nb) ? 2 ** ($nb - 1) : 0;
}

$sum = 0;
foreach ($inputs as $line) {
	if (! preg_match('/^Card *\d+:([\d\s]+)\|([\d\s]+)$/', $line, $m)) die($line);
	$win = preg_split('/\s+/', trim($m[1]));
	$our = preg_split('/\s+/', trim($m[2]));
	$sum+= count_points($win, $our);
}
printf("SUM=%d\n", $sum);
