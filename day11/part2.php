<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$size_x = strlen($inputs[0]);
$size_y = count($inputs);
print_r(["Total_lenX"=>$size_x, "Total_lenY"=>$size_y]);

// Recherche les galaxies (note les lignes et colonnes non-vides)
$galaxies = $found_y = $found_x = array();
foreach ($inputs as $y => $line) {
	foreach (str_split($line) as $x => $tile) {
		if ($tile !=='#') continue;
		$galaxies[] = [$x, $y];
		$found_x[ $x ] = true;
		$found_y[ $y ] = true;
	}
}
$ng = count($galaxies);
printf("\nGalaxies=%d / emptyY=%d & emptyX=%d\n", $ng, $size_y - count($found_y), $size_x - count($found_x));

// Calcul la distance entre 2 galaxys
function calc_distance($g1, $g2) {
	global $galaxies, $found_y, $found_x;
	list($x1, $y1) = $galaxies[$g1];
	list($x2, $y2) = $galaxies[$g2];
	// inversion si besoin pour simplifier la suite en s'assurant que x1 < x2
	if ($x1 > $x2) { $x = $x1; $x1 = $x2; $x2 = $x; }
	// calcul de distance normal (abs inutile puisque x1<x2 et y1<y2)
	$dist = ($y2 - $y1) + ($x2 - $x1);
	// ajout des distances supplémentaires pour chaque colonne et ligne vide traversée
	$more = 0;
	for ($y = $y1; $y < $y2; $y++) if (! isset($found_y[$y])) $more+= 999999;
	for ($x = $x1; $x < $x2; $x++) if (! isset($found_x[$x])) $more+= 999999;
	// affichage des résultats et retourne la distance rectifiée
	printf("Galaxies %3d & %-3d : [%03d:%03d]-[%03d:%03d] = %5d + %d.\n", $g1+1, $g2+1, $x1, $y1, $x2, $y2, $dist, $more);
	return $dist + $more;
}

// Somme des distances pour chaque paires de galaxies
$sum = 0;
for ($i = 0; $i < $ng; $i++) for ($j = $i+1; $j < $ng; $j++) $sum+= calc_distance($i, $j);
printf("\n>>> Sum=%d\n\n", $sum);
