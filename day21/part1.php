<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

#$nb_steps = 6;
$nb_steps = 64;

$width = strlen(reset($inputs)) - 1;
$height = count($inputs) - 1;

// Recherche la position de départ
$x = $y = null;
foreach ($inputs as $y => $line) {
	if (false !== ($x = strpos($line, 'S'))) break;
}
$positions = array( sprintf("%d:%d", $y, $x) => true );

// Liste des positions possibles après chaque déplacements
for ($step = 1; $step <= $nb_steps; $step++) {
	$next = [];
	// stockage des coordonnées dans une chaine en clé pour dédoublonnage automatique
	foreach (array_keys($positions) as $pos) {
		list($y, $x) = explode(':', $pos);
		if ($x > 0       && $inputs[$y][$x - 1] !== '#') $next[ sprintf("%d:%d", $y, $x - 1) ] = true;
		if ($x < $width  && $inputs[$y][$x + 1] !== '#') $next[ sprintf("%d:%d", $y, $x + 1) ] = true;
		if ($y > 0       && $inputs[$y - 1][$x] !== '#') $next[ sprintf("%d:%d", $y - 1, $x) ] = true;
		if ($y < $height && $inputs[$y + 1][$x] !== '#') $next[ sprintf("%d:%d", $y + 1, $x) ] = true;
	}
	$positions = $next;
}

printf("\nCount=%d\n\n", count($positions));
