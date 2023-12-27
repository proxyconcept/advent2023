<?php
$inputs = file('input0.txt', FILE_IGNORE_NEW_LINES);

$max_x = strlen($inputs[0]) - 1;
$max_y = count($inputs) - 1;

$cache = array();

function run($sum, $y, $x, $path, $dir, $loss) {
	global $best, $cache, $inputs, $max_x, $max_y;
	
	$dir = 1 - $dir;
	$key = join(':', [$y,$x,$dir]);
#	printf("[%02d:%02d/%d] %03d...\n", $y, $x, $dir, $loss);
	
	// cache ?
	if (array_key_exists($key, $cache)) return (null === $cache[$key]) ? null : $cache[$key] + $loss;
	
	// abandon ?
	$sum+= $loss;
	if ((null !== $best) && ($best <= $sum)) return false;

	// boucle ?
	if (array_key_exists($key, $path)) return null;
	$path[$key] = true;
	
	// arrivée ?
	if (($x == $max_x) && ($y == $max_y)) {
		if ((null === $best) || ($best > $sum)) $best = $sum;
		printf(">>> %s Found a solution [%d] :\n\t%s\n", date('H:m:s'), $sum, implode(", ", array_keys($path)));
		$cache[$key] = 0;
		return $loss;
	}
	
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
	printf("[%02d:%02d/%d] %03d : %s\n", $y, $x, $dir, $loss, json_encode($res));
	
	//
	$minloss = null;
	$aborted = $toolong = false;
	foreach ($res as $val) {
		if (null === $val) { $aborted = true; continue; }
		if (false === $val) { $toolong = true; continue; }
		if ($val < 0) { $aborted = true; $val = - $val; }
		if ($minloss === null || $val < $minloss) $minloss = $val;
	}

	if ($minloss !== null) $toolong = false;
	if ($toolong) $aborted = true;
	
	if ($aborted === false) $cache[$key] = $minloss;
	if ($minloss === null) return null;
	$loss+= $minloss;
	return ($aborted) ? -$loss : $loss;
}

function start($y, $x, $d) {
	global $best, $cache;
#	$cache = array();
	$best = null;
	$min = run(0, $y, $x, [], $d, 0);
	printf("\n=== Start [%02d:%02d] %s : Min=%d\n\n", $y, $x, $d, $min);
}


start(12, 12, 1);	//  0         =   0
start(12, 11, 0);	//  0 + 3     =   3
start(10, 11, 1);	//  3 + 3+3   =   9
start(10, 12, 0);	//  9 + 6     =  15
start( 7, 12, 1);	// 15 + 3+3+7 =  28		30
start( 7, 11, 0);	// 28 + 3     =  31		33
start( 4, 11, 1);	// 31 + 5+6+5 =  47		52
start( 4, 10, 0);	// 47 + 3     =  50		56
start( 2, 10, 1);	// 50 + 5+4   =  59		60
start( 2,  8, 0);	// 59 + 2+4   =  65		73
#start( 0,  8, 1);	// 65 + 5+3   =  73		74
#start( 0,  5, 0);	// 73 + 1+3+2 =  79		91
#start( 1,  5, 1);	// 79 + 3     =  82		98
#start( 1,  2, 0);	// 82 + 5+4+5 =  96		109
#start( 0,  2, 1);	// 96 + 1     =  97		110
#start( 0,  0, 0);	// 97 + 1+4   = 102		106
#start( 0,  0, 1);

