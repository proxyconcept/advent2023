<?php
$inputs = file('input.txt');

$sum = 0;
foreach ($inputs as $line) {
	$min = array('red' => 0, 'green' => 0, 'blue' => 0);
	if (! preg_match('/^Game \d+: (.*)$/', $line, $m)) die($line);
	foreach (explode(';', $m[1]) as $set) foreach (explode(',', $set) as $col) {
		if (! preg_match('/^ *(\d+) *([a-z]+) *$/', $col, $m)) die($col);
		if ($m[1] > $min[$m[2]]) $min[$m[2]] = $m[1];
	}
	$sum+= $min['red'] * $min['green'] * $min['blue'];
}
printf("SUM=%d\n", $sum);
