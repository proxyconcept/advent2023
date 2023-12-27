<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$delta = array('U' => [-1,0], 'D' => [1,0], 'L' => [0,-1], 'R' => [0,1]);
$tiles = array(array(true));
$x = $y = $min_x = $min_y = $max_x = $max_y = 0;

// Lecture de l'itinéraire pour noter les cases des contours (tracé)
foreach ($inputs as $line) {
	if (! preg_match('/^([RLDU]) (\d+) /', $line, $m)) die();
	list(, $d, $l) = $m;
	for ($p = 1; $p <= $l; $p++) {
		$y+= $delta[$d][0];
		$x+= $delta[$d][1];
		if (! isset($tiles[$y])) $tiles[$y] = array();
		$tiles[$y][$x] = true;
		if ($x > $max_x) $max_x = $x; elseif ($x < $min_x) $min_x = $x;
		if ($y > $max_y) $max_y = $y; elseif ($y < $min_y) $min_y = $y;
	}
}
printf("[%d - %d] x [%d - %d]\n", $min_x, $max_x, $min_y, $max_y);

// Analyse chaque case de la grille pour déterminer son état (contour/in/out)
$v_last = $v_in = array_fill($min_x, $max_x - $min_x + 1, false);
$count = 0;
for ($y = $min_y; $y <= $max_y; $y++) {
	$h_last = $h_in = false;
	for ($x = $min_x; $x <= $max_x; $x++) {
		if (isset($tiles[$y][$x])) {
			$h_last = $v_last[$x] = true;
			print('#'); $count++;
		} else {
			if ($v_last[$x] && $h_last) $v_in[$x] = $h_in = ! $h_in;
			elseif ($v_last[$x]) $v_in[$x] = $h_in;
			elseif ($h_last)     $h_in     = $v_in[$x];
			$h_last = $v_last[$x] = false;
			if ($v_in[$x] && $h_in) {
				print('='); $count++;
			} else {
				print('.');
			}
		}
	}
	print("\n");
}
printf("\nCount=%d\n\n", $count);
