<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$delta = array([0, 1], [1, 0], [0, -1], [-1, 0]); // R, D, L, U
$tiles = array(array());
$x = $y = $min_x = $min_y = $max_x = $max_y = 0;

// Lecture de l'itinéraire pour noter les cases des tournants (coins)
foreach ($inputs as $line) {
	if (! preg_match('/^[RLDU] \d+ \(#([0-9a-f]+)([0-9a-f])\)/', $line, $m)) die();
	list(, $len, $d) = $m;
	$l = hexdec($len);
	$y+= $delta[$d][0] * $l;
	$x+= $delta[$d][1] * $l;
	if (! isset($tiles[$y])) $tiles[$y] = array();
	$tiles[$y][] = $x;
	if ($x > $max_x) $max_x = $x; elseif ($x < $min_x) $min_x = $x;
	if ($y > $max_y) $max_y = $y; elseif ($y < $min_y) $min_y = $y;
}
printf("[%d - %d] x [%d - %d]\n\n", $min_x, $max_x, $min_y, $max_y);

// Analyse des noeuds ligne par ligne pour déterminer les surfaces (rectangles superposés)
$sum = 0;
$cur_parts = array();
$cur_pos_y = null;
ksort($tiles);
print_r($tiles);

foreach ($tiles as $y => $xxx) {
	printf("[%02d] CUR: %-20s ... %s\n", $y, json_encode($cur_parts), implode(',',$xxx));
	
	// Comptage des lignes précédentes
	if (null !== $cur_pos_y && ($y > ++$cur_pos_y)) {
		foreach ($cur_parts as $cur) $sum+= ($cur[1] - $cur[0] + 1) * ($y - $cur_pos_y);
	}
	
	// Recherche des nouveaux tronçons (tronçons en cours transformés selon les noeuds de la ligne)
	$new_parts = array();
	$old_parts = $cur_parts;
	$cur = array_shift($cur_parts);
	$deb = null;
	
	sort($xxx, SORT_NUMERIC);
	foreach ($xxx as $x) {
		if ($deb === null) { $deb = $x; continue; }
		$new = [ $deb, $x ]; $deb = null;
		printf("... NEW [ %d - %d ]\n", $new[0], $new[1]);
		
		// Tronçon en cours suivant si commence après la fin en cours
		while ((null !== $cur) && ($new[0] > $cur[1])) {
			$new_parts[] = $cur;
			$cur = array_shift($cur_parts);
		}
		// Nouveau tronçon si termine avant le début en cours
		if (null === $cur || $new[1] < $cur[0]) $new_parts[] = $new;
		// Prolongation au début si termine au début en cours
		elseif ($new[1] === $cur[0]) $cur[0] = $new[0];
		// Prolongation à la fin si débute à la fin en cours
		elseif ($new[0] === $cur[1]) {
			// jonction si termine au début du suivant
			if ($cur_parts && $new[1] === $cur_parts[0][0]) {
				$add = array_shift($cur_parts);
				$cur[1] = $add[1];
			} else $cur[1] = $new[1];
		}
		// Disparition du tonçon si débute et termine comme en cours
		elseif ($new[0] === $cur[0] && $new[1] === $cur[1]) $cur = array_shift($cur_parts);
		// Diminution au début si débute au début en cours
		elseif ($new[0] === $cur[0]) $cur[0] = $new[1];
		// Diminution à la fin si termine à la fin en cours
		elseif ($new[1] === $cur[1]) $cur[1] = $new[0];
		// Diminution au milieu si commence et termine dans en cours
		elseif ($new[0] > $cur[0] && $new[1] < $cur[1]) {
			$new_parts[] = [ $cur[0], $new[0] ];
			$cur[0] = $new[1];
		}
	}
	
	// Ajout des tronçons en cours restants
	while ($cur !== null) {
		$new_parts[] = $cur;
		$cur = array_shift($cur_parts);
	}
	
	// Traitement de la ligne intermédiaire (union de old & new)
	$now_parts = array();
	$tmp_parts = array_merge($old_parts, $new_parts);
	sort($tmp_parts);
	list($deb,$fin) = array_shift($tmp_parts);
	while ($tmp = array_shift($tmp_parts)) {
		if ($tmp[0] > $fin) {
			$now_parts[] = [$deb, $fin];
			list($deb,$fin) = $tmp;
		}
		elseif ($tmp[1] > $fin) $fin = $tmp[1];
	}
	$now_parts[] = [$deb, $fin];
	
	// Comptage de la ligne intermédiaire
	foreach ($now_parts as $now) $sum+= ($now[1] - $now[0] + 1);
	
	printf("[%02d] NEW: %s\n", $y, json_encode($new_parts));
	$cur_parts = $new_parts;
	$cur_pos_y = $y;
}
printf("\nSum=%d\n\n", $sum);
