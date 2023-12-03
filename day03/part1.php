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

function find_symbol($l, $x, $y) {
	global $inputs;
	if (! isset($inputs[$y])) return;
	for ($i = $x - 1; $i <= $x + $l; $i++) {
		if (! isset($inputs[$y][$i])) continue;
		if ($inputs[$y][$i] === '.') continue;
		if (ctype_digit($inputs[$y][$i])) continue;
		printf("FOUND[%s]", $inputs[$y][$i]);
		return true;
	}
	return false;
}

function is_ok($y, $x, $l) {
	if (find_symbol($l, $x, $y - 1)) return true;
	if (find_symbol($l, $x, $y    )) return true;
	if (find_symbol($l, $x, $y + 1)) return true;
	return false;
}

$sum = 0;
$idx = 0;
foreach ($inputs as $line) {
	foreach (find_numbers($line) as $pos => $res) {
		printf("[%03d x %03d] %5s\t", $pos, $idx, $res);
		if (is_ok($idx, $pos, strlen($res))) $sum+= $res;
		printf("\n");
	}
	$idx++;
}
printf("SUM=%d\n", $sum);
