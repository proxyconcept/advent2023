<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$width = strlen($inputs[0]);
$height = count($inputs);

$y_deb = 0;
$x_deb = strpos($inputs[$y_deb], '.');
$y_fin = $height - 1;
$x_fin = strpos($inputs[$y_fin], '.');
#print_r([ 'deb' => [$x_deb, $y_deb], 'fin' => [$x_fin, $y_fin] ]);

$delta = array('^' => [-1,0], 'v' => [1,0], '<' => [0,-1], '>' => [0,1]);

function walk($x, $y, $path = []) {
	global $inputs, $width, $height, $x_fin, $y_fin, $delta, $paths;
	
	// case impossible (hors limites ou forêt)
	if ($x < 0 || $x >= $width) return;
	if ($y < 0 || $y >= $height) return;
	if (($c = $inputs[$y][$x]) === '#') return;
	
	// case terminale atteinte (enregistre le chemin)
	if (($x === $x_fin) && ($y === $y_fin)) { $paths[] = $path; return; }
	
	// case déjà parcourue ? (cache du chemin effectué)
	$k = $x.':'.$y;
	if (array_key_exists($k, $path)) return;
	$path[$k] = [$x, $y];
	
	// possibilités pour la suite du chemin (réduite si pente)...
	if (array_key_exists($c, $delta)) {
		$dir = $delta[$c];
		walk($x + $dir[1], $y + $dir[0], $path);
	}
	else foreach ($delta as $dir) {
		walk($x + $dir[1], $y + $dir[0], $path);
	}
}

$paths = array();
walk($x_deb, $y_deb);
#print_r(array_map('json_encode', array_map('array_keys', $paths)));

printf("\nMax=%d\n\n", max(array_map('count', $paths)));
### /!\ memory_limit=194M
