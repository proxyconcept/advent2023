<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);


// Liste des briques en train de chuter (classées ensuite par altitude)
$bricks = array();
foreach ($inputs as $line) {
	if (! preg_match('/^(\d+),(\d+),(\d+)~(\d+),(\d+),(\d+)$/', $line, $m)) die("Invalid brick : $line\n");
	$bricks[] = array_map('intval', array( 'x1'=>$m[1], 'y1'=>$m[2], 'z1'=>$m[3], 'x2'=>$m[4], 'y2'=>$m[5], 'z2'=>$m[6] ));
}
usort($bricks, function($a,$b){ return ($a['z1'] === $b['z1']) ? $a['z2'] - $b['z2'] : $a['z1'] - $b['z1']; });
#print_r(array_map('json_encode', $bricks));


// Carte 2d du sol avec la valeur des hauteurs occupées
$x_min = min(array_column($bricks, 'x1'));
$x_max = max(array_column($bricks, 'x2'));
$y_min = min(array_column($bricks, 'y1'));
$y_max = max(array_column($bricks, 'y2'));
$size_x = $x_max - $x_min + 1;
$size_y = $y_max - $y_min + 1;
$map_heights = array_fill($x_min, $size_x, array_fill($y_min, $size_y, 0));
#print_r(array_map('json_encode', $map_heights));

// Carte 2d du sol avec le numéro de la brique occupante
$map_bricks = array_fill($x_min, $size_x, array_fill($y_min, $size_y, null));
#print_r(array_map('json_encode', $map_bricks));


// Chute des briques (en notant hauteurs atteintes, briques occupantes et chaques soutients)
$dependencies = array_fill(0, count($bricks), []);
$supporters = array_fill(0, count($bricks), []);
foreach ($bricks as $n => &$brick) {
	$h = $brick['z2'] - $brick['z1'] + 1;
	$z = 0;
	for ($x = $brick['x1']; $x <= $brick['x2']; $x++) {
		for ($y = $brick['y1']; $y <= $brick['y2']; $y++) {
			$z = max($z, $map_heights[$x][$y]);
		}
	}
	$brick['z1'] = $z + 1;
	$brick['z2'] = $z + $h;
	for ($x = $brick['x1']; $x <= $brick['x2']; $x++) {
		for ($y = $brick['y1']; $y <= $brick['y2']; $y++) {
			if ($z === 0) {
				$dependencies[$n][ '-' ] = false;
			} elseif ($z === $map_heights[$x][$y]) {
				$dependencies[$n][ $map_bricks[$x][$y] ] = true;
				$supporters[ $map_bricks[$x][$y] ][$n] = true;
			}
			$map_heights[$x][$y] = $brick['z2'];
			$map_bricks[$x][$y] = $n;
		}
	}
}
print_r(array_map('json_encode', $bricks));
print_r(array_map('json_encode', $map_heights));
print_r(array_map('json_encode', $map_bricks));

// Recherche des briques non-retirables (requises car dépendance unique d'une autre)
$required = array();
foreach ($dependencies as $brick => $depends) {
	printf("[%04d] : %s\n", $brick, join(", ", array_keys($depends)));
	if ((count($depends) === 1) && reset($depends)) $required[ key($depends) ] = true;
}
print_r(implode(', ', array_keys($required)));

// Retrait d'une brique et de celles qui en dépendent récursivement (retourne leur nombre)
function test_remove($brick, $deps) {
	global $supporters;
	$todo = array($brick);
	$removed = 0;
	while ($todo) {
		$next = array();
		foreach ($todo as $brick) {
			foreach (array_keys($supporters[$brick]) as $supported) {
				unset($deps[$supported][$brick]);
				if ($deps[$supported]) continue;
				$next[] = $supported;
				$removed++;
			}
		}
		$todo = array_unique($next);
	}
	return $removed;
}

// Additionne le nombre de briques chutant avec le retrait de chacune des briques requises
$sum = 0;
foreach (array_keys($required) as $brick) $sum+= test_remove($brick, $dependencies);
printf("\nSum=%d\n\n", $sum);
