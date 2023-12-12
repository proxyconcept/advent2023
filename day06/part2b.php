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
for ($load = 1; $load < $max_time; $load++) {
	// distance = temps restant * vitesse
	if (($max_time - $load) * $load > $min_dist) break;
}

// $nb = $max_time - 2*$load + 1
$min = $load;
$max = $max_time - $min;
$nb = $max - $min + 1;
printf("\n\nNb=%d (min/max [%d:%d])\n\n", $nb, $min, $max);
