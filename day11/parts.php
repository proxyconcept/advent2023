<?php
// Version commune pour résolution des parties 1 & 2
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

// Recherche les galaxies (note les lignes et colonnes non-vides)
$galaxies = $found_y = $found_x = array();
foreach ($inputs as $y => $line) foreach (str_split($line) as $x => $tile) {
	if ($tile !=='#') continue;
	$found_x[ $x ] = $found_y[ $y ] = true;
	$galaxies[] = [$x, $y];
}

// Calcul la distance entre 2 galaxys
function calc_distance($g1, $g2, $m) {
	global $galaxies, $found_y, $found_x;
	list($x1, $y1) = $galaxies[$g1];
	list($x2, $y2) = $galaxies[$g2];
	// inversion si besoin pour simplifier la suite en s'assurant que x1 < x2
	if ($x1 > $x2) { $x = $x1; $x1 = $x2; $x2 = $x; }
	// calcul de distance normal (abs inutile puisque x1<x2 et y1<y2)
	$dist = $y2 - $y1 + $x2 - $x1;
	// ajout des distances supplémentaires pour chaque colonne et ligne vide traversée
	for ($y = $y1; $y < $y2; $y++) if (! isset($found_y[$y])) $dist+= $m - 1;
	for ($x = $x1; $x < $x2; $x++) if (! isset($found_x[$x])) $dist+= $m - 1;
	// affichage des résultats et retourne la distance rectifiée
	printf("Galaxies %3d & %-3d : [%03d:%03d]-[%03d:%03d] = %d\n", $g1+1, $g2+1, $x1, $y1, $x2, $y2, $dist);
	return $dist;
}

// Somme des distances pour chaque paires de galaxies
$ng = count($galaxies);
$sum1 = 0; for ($i = 0; $i < $ng; $i++) for ($j = $i+1; $j < $ng; $j++) $sum1+= calc_distance($i, $j, 2);
$sum2 = 0; for ($i = 0; $i < $ng; $i++) for ($j = $i+1; $j < $ng; $j++) $sum2+= calc_distance($i, $j, 1000000);
printf("\nSum1=%d\nSum2=%d\n", $sum1, $sum2);
