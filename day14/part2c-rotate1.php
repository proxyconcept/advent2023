<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$nbcycles = 1000000000;
$nbcycles = 1000000;
$nbcycles = 10000;

$tiles = array(); foreach ($inputs as $line) $tiles[] = str_split($line);
$width = count(reset($tiles));
$height = count($tiles);

for ($c = 0; $c < $nbcycles; $c++) for ($i = 0; $i < 4; $i++) {
	$new = array();
	for ($x = 0; $x < $width; $x++) {
		$row = array();
		for ($pos = $y = 0; $y < $height; $y++) {
			if     ($tiles[$y][$x] == '#') { $row[] = '#'; $pos = $y + 1; }
			elseif ($tiles[$y][$x] == 'O') { $row[] = '.'; $row[$pos++] = 'O'; }
			else   $row[] = '.';
		}
		$new[] = array_reverse($row);
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
### input0: 27s x1000
### input : 31s x100000
