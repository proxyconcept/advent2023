<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$nbcycles = 1000000000;
$nbcycles = 1000000;
$nbcycles = 10000;

$tiles = array(); foreach ($inputs as $line) $tiles[] = str_split($line);
$width = count(reset($tiles));
$height = count($tiles);

$tcache = array();

for ($c = 0; $c < $nbcycles; $c++) for ($i = 0; $i < 4; $i++) {
	$new = array();
	for ($x = 0; $x < $width; $x++) {
		$key = implode('', array_column($tiles, $x));
		if (array_key_exists($key, $tcache)) { $new[] = $tcache[$key]; continue; }
		
		$row = array();
		for ($pos = $y = 0; $y < $height; $y++) {
			if     ($tiles[$y][$x] == '.') $row[] = '.';
			elseif ($tiles[$y][$x] == 'O') { $row[] = '.'; $row[$pos++] = 'O'; }
			else                           { $row[] = '#'; $pos = $y + 1; }
		}
		$new[] = $tcache[$key] = array_reverse($row);
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
### input0: 12s x1000
### input :  8s x100000
