<?php

$digits = array('one'=>1,'two'=>2,'three'=>3,'four'=>4,'five'=>5,'six'=>6,'seven'=>7,'eight'=>8,'nine'=>9);
$findme = join('|', array_keys($digits)) . '|' . join('|', array_values($digits));
var_dump($findme);

function calc1x($str) {
	global $digits, $findme;
	
	preg_match_all('/'.$findme.'/', $str, $m) or die($str);
	$d1 = reset($m[0]); if (! ctype_digit($d1)) $d1 = $digits[$d1];
	$d2 = end($m[0]);   if (! ctype_digit($d2)) $d2 = $digits[$d2];
	
	printf("[%s] [%s] %s\n", $d1, $d2, $str);
	return $d1 . $d2;
}

function calc1($str) {
	global $digits, $findme;
	
	if (! preg_match('/('.$findme.')/', $str, $m)) die($str);
	$d1 = ctype_digit($m[1]) ? $m[1] : $digits[$m[1]];
	
	if (! preg_match('/.*('.$findme.')/', $str, $m)) die($str);
	$d2 = ctype_digit($m[1]) ? $m[1] : $digits[$m[1]];
	
	printf("[%s] [%s] %s\n", $d1, $d2, $str);
	return $d1 . $d2;

}

$sum1 = 0;
$inputs = file('input.txt');
foreach ($inputs as $line) {
	$sum1+= calc1($line);
}
printf("SUM1=%d\n", $sum1);
