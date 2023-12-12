<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

list(, $time) = explode(':', $inputs[0], 2);
list(, $dist) = explode(':', $inputs[1], 2);

$max_time = preg_replace('/[^\d]/', '', $time);
$min_dist = preg_replace('/[^\d]/', '', $dist);
print_r(["max_time"=>$max_time, "min_dist"=>$min_dist]);

### Exemple :
###   temps max = 10ms
###   - 1ms load / 9ms move (@ 1mm/ms) => 9mm
###   - 2ms load / 8ms move (@ 2mm/ms) => 16mm
###   - 5ms load / 5ms move (@ 5mm/ms) => 25mm
###   - 8ms load / 2ms move (@ 8mm/ms) => 16mm

// Recherche la plus courte durée de chargement permettant de dépasser la distance
function find_min($min, $max) {
	global $max_time, $min_dist;
	
	if ($min === $max) return $min;
	$val = $min + intdiv($max - $min, 2);       // valeur au milieu entre min et max
	printf("%10d\t", $val);
	
	// temps restant * vitesse (temps de charge) => distance
	if (($max_time - $val) * $val > $min_dist)
		return find_min($min, $val);            // soit recherche dans la première moitiée
	return find_min($val + 1, $max);            // soit recherche dans la seconde moitiée
}

// Lancement de la recherche optimisée par dichotomie
$min = find_min(1, $max_time);
$max = $max_time - $min;
$nb = $max - $min + 1;
printf("\n\nNb=%d (min/max [%d:%d])\n\n", $nb, $min, $max);
