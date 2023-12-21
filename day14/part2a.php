<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$nbcycles = 1000000000;
$nbcycles = 1000000;
$nbcycles = 10000;

$width = strlen($inputs[0]);
$height = count($inputs);

// Effectue les n déplacements vers nord+ouest+sud+est
for ($c = 0; $c < $nbcycles; $c++) {
	// move north
	for ($x = 0; $x < $width; $x++) for ($pos = $y = 0; $y < $height; $y++) {
		if     ($inputs[$y][$x] == '#') $pos = $y + 1;
		elseif ($inputs[$y][$x] == 'O') {
			$inputs[$y    ][$x] = '.';
			$inputs[$pos++][$x] = 'O';
		}
	}
	// move west
	for ($y = 0; $y < $height; $y++) for ($pos = $x = 0; $x < $width; $x++) {
		if     ($inputs[$y][$x] == '#') $pos = $x + 1;
		elseif ($inputs[$y][$x] == 'O') {
			$inputs[$y][$x    ] = '.';
			$inputs[$y][$pos++] = 'O';
		}
	}
	// move south
	for ($x = 0; $x < $width; $x++) for ($pos = $y = $height - 1; $y >= 0; $y--) {
		if     ($inputs[$y][$x] == '#') $pos = $y - 1;
		elseif ($inputs[$y][$x] == 'O') {
			$inputs[$y    ][$x] = '.';
			$inputs[$pos--][$x] = 'O';
		}
	}
	// move east
	for ($y = 0; $y < $height; $y++) for ($pos = $x = $width - 1; $x >= 0; $x--) {
		if     ($inputs[$y][$x] == '#') $pos = $x - 1;
		elseif ($inputs[$y][$x] == 'O') {
			$inputs[$y][$x    ] = '.';
			$inputs[$y][$pos--] = 'O';
		}
	}
}

// Additionne les poids au nord en traitant colonne par colonne...
$sum = 0;
for ($x = 0; $x < $width; $x++) {
	$pos = 0;
	for ($y = 0; $y < $height; $y++) {
		if     ($inputs[$y][$x] == 'O') $sum+= $height - $pos++;
		elseif ($inputs[$y][$x] == '#') $pos = $y + 1;
	}
}
printf("\nSum=%d\n\n", $sum);
### input0: 26s x1000
### input : 26s x100000
