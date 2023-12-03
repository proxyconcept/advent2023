<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

function find_numbers($line) {
	$res = array();
	$idx = 0;
	$pos = 0;
	$cur = '';
	foreach (str_split($line) as $char) {
		if (ctype_digit($char)) {
			if (! $pos) $pos = $idx;
			$cur.= $char;
		}
		else if (strlen($cur)) {
			$res[$pos] = $cur;
			$pos = 0;
			$cur = '';
		}
		$idx++;
	}
	if (strlen($cur)) $res[$pos] = $cur;
	return $res;
}

function find_gears($y, $x, $l) {
	global $inputs;
	$res = array();
	for ($j = $y - 1; $j <= $y + 1; $j++) {
		if (! isset($inputs[$j])) continue;
		for ($i = $x - 1; $i <= $x + $l; $i++) {
			if (! isset($inputs[$j][$i])) continue;
			if ($inputs[$j][$i] === '*') $res[] = $i.'x'.$j;
		}
	}
	return $res;
}

$idx = 0;
$gears = array();
foreach ($inputs as $line) {
	foreach (find_numbers($line) as $pos => $res) {
#		printf("[%03d x %03d] %5s\t", $pos, $idx, $res);
		foreach (find_gears($idx, $pos, strlen($res)) as $gear) {
			if (! isset($gears[ $gear ])) $gears[ $gear ] = array();
			$gears[ $gear ][] = $res;
		}
	}
	$idx++;
}
var_dump($gears);

$sum = 0;
foreach ($gears as $numbers) {
	if (count($numbers) != 2) continue;
	$sum+= $numbers[0] * $numbers[1];
}
printf("SUM=%d\n", $sum);
