<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);
$inputs[] = "";


// Retourne la symétrie trouvée sur une carte (avec un nombre de différences donné)
function resolv_card($card, $diff = 0) {
	print_r($card);
	$w = strlen($card[0]);
	$h = count($card);
	
	// recherche d'une symétrie verticale
	for ($x = 1; $x < $w; $x++) {
		$d = 0;
		printf("\n>>> Test col %02d/%02d :", $x, $w);
		for ($y = 0; $y < $h; $y++) {
			$l = min($x, $w - $x);
			printf("\n  * y=%02d/%02d (%02d)", $y, $h, $l);
			$p1 = $p2 = '';
			for ($p = 0; $p < $l; $p++) {
				$c1 = $card[$y][$x - $p - 1];  $p1.= $c1;
				$c2 = $card[$y][$x + $p];      $p2.= $c2;
				if (($c1 !== $c2) && (++$d > $diff)) {
					printf(" %{$h}s ! %-{$h}s (%d)", $p1, $p2, $d);
					continue 3;
				}
			}
			printf(" %{$h}s | %-{$h}s (%d)", $p1, $p2, $d);
		}
		if ($d == $diff) return $x;
	}
	// recherche d'une symétrie horizontale
	for ($y = 1; $y < $h; $y++) {
		$d = 0;
		printf("\n>>> Test row %02d/%02d :", $y, $h);
		for ($x = 0; $x < $w; $x++) {
			$l = min($y, $h - $y);
			printf("\n  * x=%02d/%02d (%02d)", $x, $w, $l);
			$p1 = $p2 = '';
			for ($p = 0; $p < $l; $p++) {
				$c1 = $card[$y - $p - 1][$x];  $p1.= $c1;
				$c2 = $card[$y + $p][$x];      $p2.= $c2;
				if (($c1 !== $c2) && (++$d > $diff)) {
					printf(" %{$w}s ! %-{$w}s (%d)", $p1, $p2, $d);
					continue 3;
				}
			}
			printf(" %{$w}s | %-{$w}s (%d)", $p1, $p2, $d);
		}
		if ($d == $diff) return $y * 100;
	}
	return 0;
}

// Lecture des données pour traitements carte par carte
$sum = 0;
$card = array();
foreach ($inputs as $line) {
	if ($line !== "") {
		$card[] = trim($line);
		continue;
	}
	$res = resolv_card($card, 1); // avec 1 seule différence
	$sum+= $res;
	$card = array();
	printf("\nRes=%d\n", $res);
}
printf("\nSum=%d\n\n", $sum);
