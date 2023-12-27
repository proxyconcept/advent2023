<?php
$inputs = file('input0.txt', FILE_IGNORE_NEW_LINES);

$width = strlen($inputs[0]);
$height = count($inputs);

$delta = array(
	'U' => [ -1,  0 ],
	'D' => [ +1,  0 ],
	'L' => [  0, -1 ],
	'R' => [  0, +1 ],
);

function run($sum, $y, $x, $path, $dir, $len) {
	global $best, $inputs, $width, $height, $delta;
	
	// impossible d'avancer plus de 3 fois dans la même direction
	if ($len > 3) return false;
	
	// application du déplacement (fin de parcours si hors limite)
	list($dy, $dx) = $delta[$dir];
	$y+= $dy; if (($y < 0) || ($y >= $height)) return false;
	$x+= $dx; if (($x < 0) || ($x >= $width)) return false;
	
	// arrêt en cas de détection de boucle sur le chemin pris
	$key = join(':', [$y,$x,$dir,$len]);
	if (array_key_exists($key, $path)) return false;
	$path[$key] = true;
	
	// ajout de la perte de chaleur de la case (fin si au-délà du meilleur chemin trouvé)
	$sum+= intval($inputs[$y][$x]);
	if ($best && $best <= $sum) return false;
	
	// arrivée ?
	if (($x == $width - 1) && ($y == $height - 1)) {
		$best = $sum;
		printf(">>> Found a solution [%d] :\n\t%s\n", $best, implode("\t", array_keys($path)));
		return true;
	}
	
	// test les différentes suite de parcours possible pour la case suivante...
	switch ($dir) {
		case 'R' :
			run($sum, $y, $x, $path, 'D', 1);
			run($sum, $y, $x, $path, 'R', 1 + $len);
			run($sum, $y, $x, $path, 'U', 1);
			break;
		case 'D' :
			run($sum, $y, $x, $path, 'R', 1);
			run($sum, $y, $x, $path, 'D', 1 + $len);
			run($sum, $y, $x, $path, 'L', 1);
			break;
		case 'L' :
			run($sum, $y, $x, $path, 'D', 1);
			run($sum, $y, $x, $path, 'L', 1 + $len);
			run($sum, $y, $x, $path, 'U', 1);
			break;
		case 'U' :
			run($sum, $y, $x, $path, 'R', 1);
			run($sum, $y, $x, $path, 'U', 1 + $len);
			run($sum, $y, $x, $path, 'L', 1);
			break;
	}
}

$best = 0; run(0, 0, -1, [], 'R', 0);
printf("\nSum=%d\n\n", $best);
