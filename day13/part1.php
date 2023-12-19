<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);
$inputs[] = "";

// Retourne la symétrie trouvée sur une carte
function resolv_card($card) {
	print_r($card);
	$w = strlen($card[0]);
	$h = count($card);
	
	// recherche d'une symétrie verticale
	for ($x = 1; $x < $w; $x++) {
		for ($y = 0; $y < $h; $y++) {
			$l = min($x, $w - $x);
			$p1 = substr($card[$y], $x - $l, $l);
			$p2 = substr($card[$y], $x, $l);
			printf("[%d] %10s | %-10s (%d)\n", $x, $p1, $p2, $l);
			if ($p1 != strrev($p2)) continue 2;
		}
		return $x;
	}
	// recherche d'une symétrie horizontale
	for ($y = 1; $y < $h; $y++) {
		for ($x = 0; $x < $w; $x++) {
			$l = min($y, $h - $y);
			for ($p = 0; $p < $l; $p++) {
				$c1 = $card[$y - $p - 1][$x];
				$c2 = $card[$y + $p][$x];
				printf("[%d x %d] %10s | %-10s (%d)\n", $y, $x, $c1, $c2, $l);
				if ($c1 !== $c2) continue 3;
			}
		}
		return $y * 100;
	}
	return 0;
}

// Lecture des données pour traitements carte par carte
$res = 0;
$card = array();
foreach ($inputs as $line) {
	if ($line !== "") {
		$card[] = trim($line);
		continue;
	}
	$res+= resolv_card($card);
	$card = array();
}
printf("\nRes=%d\n\n", $res);
