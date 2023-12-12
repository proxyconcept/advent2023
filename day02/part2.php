<?php
$inputs = file('input.txt');

function calc($str) {
	$min = array('red' => 0, 'green' => 0, 'blue' => 0);
	foreach (explode(';', $str) as $set) {
		foreach (explode(',', $set) as $col) {
			if (! preg_match('/^ *(\d+) *([a-z]+) *$/', $col, $m)) die($col);
			list(, $nb, $color) = $m;
			if ($nb > $min[$color]) $min[$color] = $nb;
		}
	}
	return $min['red'] * $min['green'] * $min['blue'];
}

$sum = 0;
foreach ($inputs as $line) {
	if (! preg_match('/^Game \d+: (.*)$/', $line, $m)) die($line);
	$sum+= calc($m[1]);
}
printf("SUM=%d\n", $sum);
