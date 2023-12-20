<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$w = strlen($inputs[0]);
$h = count($inputs);

for ($s = $x = 0; $x < $w; $x++) for ($p = $y = 0; $y < $h; $y++) {
	if     ($inputs[$y][$x] == 'O') $s+= $h - $p++;
	elseif ($inputs[$y][$x] == '#') $p = $y + 1;
}
printf("\nSum=%d\n\n", $s);
