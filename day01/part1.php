<?php

function calc1($str) {
	$digits = str_split(preg_replace('/[^\d]/', '', $str));
	return reset($digits) . end($digits);
}

function calc2($str) {
	if (! preg_match('/^[^\d]*(\d)(.*(\d))?[^\d]*$/', $str, $m)) die($m);
	return $m[1] . (isset($m[2]) ? $m[3] : $m[1]);
}

$sum1 = $sum2 = 0;
$inputs = file('input.txt');
foreach ($inputs as $line) {
	$sum1+= calc1($line);
	$sum2+= calc2($line);

}
printf("SUM1=%d\n", $sum1);
printf("SUM2=%d\n", $sum2);
