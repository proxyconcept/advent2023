<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$x_max = strlen($inputs[0]) - 1;
$y_max = count($inputs) - 1;

$delta = array('^' => [-1,0], 'v' => [1,0], '<' => [0,-1], '>' => [0,1]);

function walk($x, $y, $path = []) {
	global $inputs, $x_max, $y_max, $delta, $length;
	
	// case impossible (hors limites ou forêt)
	if ($y < 0) return;
	$c = $inputs[$y][$x];
	if ($c === '#') return;
	
	// case terminale atteinte (nouvelle longueur max ?)
	if ($y === $y_max) {
		$l = count($path);
		printf(">>> Found path : %d\n", $l);
		if ($l > $length) $length = $l;
		return;
	}
	
	// case déjà parcourue ? (cache du chemin effectué)
	$k = $x.':'.$y;
	if (array_key_exists($k, $path)) return;
	$path[$k] = true;
	
	// possibilités pour la suite du chemin (réduite si pente)...
	if (array_key_exists($c, $delta)) {
		$dir = $delta[$c];
		walk($x + $dir[1], $y + $dir[0], $path);
	}
	else foreach ($delta as $dir) {
		walk($x + $dir[1], $y + $dir[0], $path);
	}
}

$length = 0;
walk(strpos($inputs[0], '.'), 0);

printf("\nLength=%d\n\n", $length);
### /!\ memory_limit=134M
