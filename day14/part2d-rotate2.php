<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$nbcycles = 1000000000;
$nbcycles = 1000000;
$nbcycles = 10000;

$tiles = array(); foreach ($inputs as $line) $tiles[] = str_split($line);
$width = count(reset($tiles));
$height = count($tiles);

$tpl = array_fill(0, $width, array_fill(0, $height, '.'));
for ($c = 0; $c < $nbcycles; $c++) for ($i = 0; $i < 4; $i++) {
	$new = $tpl;
	for ($x = 0; $x < $width; $x++) for ($pos = $y = 0; $y < $height; $y++) {
		if     ($tiles[$y][$x] == '#') { $new[$x][$height - 1 - $y] = '#'; $pos = $y + 1; }
		elseif ($tiles[$y][$x] == 'O') { $new[$x][$height - 1 - $y] = '.'; $new[$x][$height - 1 - $pos++] = 'O'; }
	}
	$tiles = $new;
}

// Additionne les poids au nord en traitant colonne par colonne...
for ($sum = $x = 0; $x < $width; $x++) {
	for ($pos = $y = 0; $y < $height; $y++) {
		if     ($tiles[$y][$x] == 'O') $sum+= $height - $pos++;
		elseif ($tiles[$y][$x] == '#') $pos = $y + 1;
	}
}
printf("\nSum=%d\n\n", $sum);
### input0: 29s x1000
### input : 31s x100000
