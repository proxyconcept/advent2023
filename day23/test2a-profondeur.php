<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$x_max = strlen($inputs[0]) - 1;
$y_max = count($inputs) - 1;

function walk($x, $y, $path = []) {
	global $inputs, $x_max, $y_max, $len_max;
	
	// case impossible (forêt)
	if ($inputs[$y][$x] === '#') return;
	
	// case terminale atteinte (nouvelle longueur max ?)
	if ($y === $y_max) {
		$l = count($path);
		printf(">>> Found path : %d\n", $l);
		if ($l > $len_max) $len_max = $l;
		return;
	}
	
	// case déjà parcourue ? (cache du chemin effectué)
	$k = $y * ($x_max + 1) + $x;
	if (in_array($k, $path)) return;
	$path[] = $k;
	
	// possibilités pour la suite du chemin...
	if ($x >      1) walk($x - 1, $y, $path);
	if ($y >      1) walk($x, $y - 1, $path);
	if ($x < $x_max) walk($x + 1, $y, $path);
	if ($y < $y_max) walk($x, $y + 1, $path);
}

$len_max = 0;
walk(strpos($inputs[0], '.'), 0);

printf("\nLength=%d\n\n", $len_max);
### /!\ memory_limit > 1G & timers ???...
