<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$nbcycles = 1000000000;

$width = strlen($inputs[0]);
$height = count($inputs);

// Effectue un enchainement de déplacements nord+ouest+sud+est
function cycle_tilts() {
	global $inputs, $width, $height;
	
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
function calc_load($tiles) {
	$sum = 0;
	foreach (array_reverse($tiles) as $p => $row) {
		for ($x = 0; $x < strlen($row); $x++) {
			if ($row[$x] === 'O') $sum+= $p + 1;
		}
	}
	return $sum;
}

// Cycles avec interruption dès retour à une disposition précédente
$cache = array();
for ($cycle = 0; $cycle < $nbcycles; $cycle++) {
	$key = implode('|', $inputs);
	if (array_key_exists($key, $cache)) break;
	cycle_tilts();
	$cache[$key] = $cycle;
}

// Début de la boucle, longueur de la boucle, reste à effectuer
$deb = $cache[$key];
$len = $cycle - $deb;
$mod = ($nbcycles - $deb) % $len;
printf("\n>>> Loop detected %d => %d (%d) : todo %d cycles\n", $cycle, $deb, $len, $mod);

#for ($c = 0; $c < $mod; $c++) cycle_tilts();
#printf("\nSum=%d\n\n", calc_load($inputs));

// Utilisation du cache pour retrouver la disposition finale
foreach ($cache as $key => $cycle) if ($cycle === $deb + $mod) break;
printf("\nSum=%d\n\n", calc_load(explode('|', $key)));
