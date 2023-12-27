<?php
$inputs = file('input0.txt', FILE_IGNORE_NEW_LINES);

$max_x = strlen($inputs[0]) - 1;
$max_y = count($inputs) - 1;

function run($sum, $y, $x, $path, $dir, $loss) {
	global $best, $inputs, $max_x, $max_y;
	
	$sum+= $loss;
	$dir = 1 - $dir;
	$key = join(':', [$y,$x,$dir]);
#	printf("[%02d:%02d/%d] %03d...\n", $y, $x, $dir, $loss);
	
	// boucle ?
	if (array_key_exists($key, $path)) return null;
	$path[$key] = true;
	
	// arrivée ?
	if (($x == $max_x) && ($y == $max_y)) {
		if ((null === $best) || ($best > $sum)) $best = $sum;
#		printf(">>> %s Found a solution [%d] :\n\t%s\n", date('H:m:s'), $sum, implode(", ", array_keys($path)));
		return $loss;
	}
	// abandon ?
	if ((null !== $best) && ($best <= $sum)) return null;
	
	// suivants ?
	$res = array();
	if ($dir) {
		for ($todo = 0, $l = 1; $l < 4; $l++) {
			if ($x + $l > $max_x) break;
			$todo+= $inputs[$y][$x + $l];
			$res[] = run($sum, $y, $x + $l, $path, $dir, $todo);
		}
		for ($todo = 0, $l = 1; $l < 4; $l++) {
			if ($x - $l < 0) break;
			$todo+= $inputs[$y][$x - $l];
			$res[] = run($sum, $y, $x - $l, $path, $dir, $todo);
		}
	} else {
		for ($todo = 0, $l = 1; $l < 4; $l++) {
			if ($y + $l > $max_y) break;
			$todo+= $inputs[$y + $l][$x];
			$res[] = run($sum, $y + $l, $x, $path, $dir, $todo);
		}
		for ($todo = 0, $l = 1; $l < 4; $l++) {
			if ($y - $l < 0) break;
			$todo+= $inputs[$y - $l][$x];
			$res[] = run($sum, $y - $l, $x, $path, $dir, $todo);
		}
	}
#	printf("[%02d:%02d/%d] %03d : %s\n", $y, $x, $dir, $loss, json_encode($res));
	
	//
	$minloss = null;
	foreach ($res as $val) {
		if (null === $val) continue;
		if (null === $minloss || $val < $minloss) $minloss = $val;
	}
	return ($minloss === null) ? null : $loss + $minloss;
}

function start($y, $x, $d) {
	global $best;
	$best = null;
	$min = run(0, $y, $x, [], $d, 0);
	printf("\n=== Start [%02d:%02d] %s : Min=%d\n\n", $y, $x, $d, $min);
}

start(12, 12, 1);	// 3
start(12, 11, 0);	// 6
start(10, 11, 1);	// 15
start(10, 12, 0);	// 18
start( 7, 12, 1);	// -31
start( 7, 11, 0);	// -36
start( 4, 11, 1);	// -50 /vs 53
start( 4, 10, 0);	// -55
start( 2, 10, 1);	// -65 vs 61
start( 2,  8, 0);
start( 0,  8, 1);	// (-74)
start( 0,  5, 0);
start( 1,  5, 1);
start( 1,  2, 0);
start( 0,  2, 1);
start( 0,  0, 0);

