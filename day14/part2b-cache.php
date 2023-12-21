<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$nbcycles = 1000000000;
$nbcycles = 1000000;
$nbcycles = 10000;

$width = strlen($inputs[0]);
$height = count($inputs);

$ycache = array();

// Effectue les n déplacements vers nord+ouest+sud+est (avec cache sur les positions pour ouest & est)
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
	for ($y = 0; $y < $height; $y++) {
		$key = $inputs[$y];
		if (isset($ycache[ $key ])) { $inputs[$y] = $ycache[ $key ]; continue; }
		for ($pos = $x = 0; $x < $width; $x++) {
			if     ($inputs[$y][$x] == '#') $pos = $x + 1;
			elseif ($inputs[$y][$x] == 'O') {
				$inputs[$y][$x    ] = '.';
				$inputs[$y][$pos++] = 'O';
			}
		}
		$ycache[ $key ] = $inputs[$y];
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
	for ($y = 0; $y < $height; $y++) {
		$key = strrev($inputs[$y]);
		if (isset($ycache[ $key ])) { $inputs[$y] = strrev($ycache[ $key ]); continue; }
		for ($pos = $x = $width - 1; $x >= 0; $x--) {
			if     ($inputs[$y][$x] == '#') $pos = $x - 1;
			elseif ($inputs[$y][$x] == 'O') {
				$inputs[$y][$x    ] = '.';
				$inputs[$y][$pos--] = 'O';
			}
		}
		$ycache[ $key ] = strrev($inputs[$y]);
	}
}

// Additionne les poids au nord en traitant colonne par colonne...
for ($sum = $x = 0; $x < $width; $x++) {
	for ($pos = $y = 0; $y < $height; $y++) {
		if     ($inputs[$y][$x] == 'O') $sum+= $height - $pos++;
		elseif ($inputs[$y][$x] == '#') $pos = $y + 1;
	}
}
printf("\nSum=%d\n\n", $sum);
### input0: 15s x1000
### input : 13s x100000
