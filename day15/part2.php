<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

// Détermine le numéro de la box à partir du label
function hashmap($label) {
	$res = 0;
	foreach (str_split($label) as $char) {
		$res+= ord($char);
		$res*= 17;
		$res = $res % 256;
	}
	return $res;
}

// Place les lentilles dans les box selon les étapes
$boxes = array_fill(0, 256, []);
foreach (explode(',', $inputs[0]) as $step) {
	if (! preg_match('/^([a-z]+)([=-])(\d*)$/', $step, $m)) die("Invalid step : $step\n");
	list(, $label, $op, $focal) = $m;
	
	$box = hashmap($label);
	if ($op === '=') $boxes[$box][ $label ] = $focal;
	elseif (array_key_exists($label, $boxes[$box])) unset($boxes[$box][$label]);
}

// Additionne les puissances de chaques lentilles
$sum = 0;
foreach ($boxes as $n => $box) {
	$pos = 0;
	foreach ($box as $label => $focal) {
		$sum+= ($n + 1) * (++$pos) * $focal;
	}
}
printf("\nSum=%d\n\n", $sum);
