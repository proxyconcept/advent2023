<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$nbcycles = 1000000000;
$nbcycles = 1000000;
$nbcycles = 100000;

$tiles = array(); foreach ($inputs as $line) $tiles[] = str_split($line);
$x_max = strlen($inputs[0]) - 1;
$y_max = count($inputs) - 1;

$tcache = array();

$tpl = array_fill(0, $y_max + 1, '.');
for ($c = 0; $c < $nbcycles; $c++) for ($i = 0; $i < 4; $i++) {
	$new = array();
	for ($x = 0; $x <= $x_max; $x++) {
		$key = implode('', array_column($tiles, $x));
		if (isset($tcache[$key])) { $new[] = $tcache[$key]; continue; }
		
		$row = $tpl;
		for ($pos = $y = 0; $y <= $y_max; $y++) {
			if     ($tiles[$y][$x] == '#') { $row[$y_max - $y] = '#'; $pos = $y + 1; }
			elseif ($tiles[$y][$x] == 'O') { $row[$y_max - $y] = '.'; $row[$y_max - $pos++] = 'O'; }
		}
		$new[] = $tcache[$key] = $row;
	}
	$tiles = $new;
}

// Additionne les poids au nord en traitant colonne par colonne...
for ($sum = $x = 0; $x <= $x_max; $x++) {
	for ($pos = $y = 0; $y <= $y_max; $y++) {
		if     ($tiles[$y][$x] == 'O') $sum+= $y_max + 1 - $pos++;
		elseif ($tiles[$y][$x] == '#') $pos = $y + 1;
	}
}
printf("\nSum=%d\n\n", $sum);
### input0: 12s x1000
### input :  8s x100000
### input :  82s x10000
