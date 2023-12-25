<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$sum = 0;
foreach (explode(',', $inputs[0]) as $step) {
	$res = 0;
	foreach (str_split($step) as $char) {
		$res+= ord($char);
		$res*= 17;
		$res = $res % 256;
	}
	$sum+= $res;
}
printf("\nSum=%d\n\n", $sum);
