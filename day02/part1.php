<?php
$inputs = file('input.txt');

function is_ok($str) {
	foreach (explode(';', $str) as $set) {
		foreach (explode(',', $set) as $col) {
			if (! preg_match('/^ *(\d+) *([a-z]+) *$/', $col, $m)) die($col);
			list(, $nb, $color) = $m;
			switch ($color) {
				case 'red'   : if ($nb > 12) return false;
				case 'green' : if ($nb > 13) return false;
				case 'blue'  : if ($nb > 14) return false;
			}
		}
	}
	return true;
}

$sum = 0;
foreach ($inputs as $line) {
	if (! preg_match('/^Game (\d+): (.*)$/', $line, $m)) die($line);
	list(, $id, $data) = $m;
	if (is_ok($data)) $sum+= $id;
}
printf("SUM=%d\n", $sum);
