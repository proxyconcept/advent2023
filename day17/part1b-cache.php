<?php
$inputs = file('input0.txt', FILE_IGNORE_NEW_LINES);

$max_x = strlen($inputs[0]) - 1;
$max_y = count($inputs) - 1;

$cache = array();

function run($sum, $y, $x, $path, $dir, $len) {
	global $best, $cache, $inputs, $max_x, $max_y;
	
	$key = join(':', [$y,$x,$dir,$len]);
	if (array_key_exists($key, $cache)) return $cache[$key];
	
	// arrêt en cas de détection de boucle sur le chemin pris
	if (array_key_exists($key, $path)) return 'LOOP';
	$path[$key] = true;
	
	// ajout de la perte de chaleur de la case (fin si au-délà du meilleur chemin trouvé)
	if ($len) $sum+= intval($inputs[$y][$x]);
#	if ($best && $best <= $sum) return 'LONG';
	
	// arrivée ?
	if (($x == $max_x) && ($y == $max_y)) {
		if (! $best || $best > $sum) $best = $sum;
#		$best = $sum;
		printf(">>> %s Found a solution [%d] :\n\t%s\n", date('H:m:s'), $sum, implode(", ", array_keys($path)));
		return $cache[ $key ] = intval($inputs[$y][$x]);
	}
	if ($best && $best <= $sum) return 'LONG';
	
	// test les différentes suite de parcours possible pour la case suivante...
	$res = array();
	switch ($dir) {
		case 'D' :
			$res['U'] = 'CANT';
			$res['R'] = ($x < $max_x)             ? run($sum, $y,     $x + 1, $path, 'R', 1)        : 'CANT';
			$res['D'] = ($y < $max_y && $len < 3) ? run($sum, $y + 1, $x,     $path, 'D', 1 + $len) : 'CANT';
			$res['L'] = ($x > 0)                  ? run($sum, $y,     $x - 1, $path, 'L', 1)        : 'CANT';
			break;
		case 'R' :
			$res['L'] = 'CANT';
			$res['D'] = ($y < $max_y)             ? run($sum, $y + 1, $x,     $path, 'D', 1)        : 'CANT';
			$res['R'] = ($x < $max_x && $len < 3) ? run($sum, $y,     $x + 1, $path, 'R', 1 + $len) : 'CANT';
			$res['U'] = ($y > 0)                  ? run($sum, $y - 1, $x,     $path, 'U', 1)        : 'CANT';
			break;
		case 'U' :
			$res['D'] = 'CANT';
			$res['R'] = ($x < $max_x)             ? run($sum, $y,     $x + 1, $path, 'R', 1)        : 'CANT';
			$res['U'] = ($y > 0      && $len < 3) ? run($sum, $y - 1, $x,     $path, 'U', 1 + $len) : 'CANT';
			$res['L'] = ($x > 0)                  ? run($sum, $y,     $x - 1, $path, 'L', 1)        : 'CANT';
			break;
		case 'L' :
			$res['R'] = 'CANT';
			$res['D'] = ($y < $max_y)             ? run($sum, $y + 1, $x,     $path, 'D', 1)        : 'CANT';
			$res['L'] = ($x > 0      && $len < 3) ? run($sum, $y,     $x - 1, $path, 'L', 1 + $len) : 'CANT';
			$res['U'] = ($y > 0)                  ? run($sum, $y - 1, $x,     $path, 'U', 1)        : 'CANT';
			break;
	}
	
	//
	$minloss = $aborted = $toolong = false;
	foreach ($res as $val) {
		if ($val === 'CANT') continue;
		if ($val === 'LONG') { $toolong = true; continue; }
		if ($val === 'LOOP') { $aborted = true; continue; }
		if ($val  <    0   ) { $aborted = true; $val = -$val; }
		if ($minloss === false || $val < $minloss) $minloss = $val;
	}
	// aucune bonne réponse (seulement du CANT|LOOP|LONG)
	if (! $minloss) {
		if ($aborted) return 'LOOP';
		if ($toolong) return 'LONG';
		return $cache[$key] = 'CANT';
	} else {
		if ($len) $minloss+= intval($inputs[$y][$x]);
		if ($aborted) return - $minloss;
		if ($toolong) return - $minloss;			// erreurs si cas ignoré ?
		return $cache[$key] = $minloss;
	}
}

function start($y, $x, $d, $l) {
	global $best, $cache;
	$cache = array();
	$best = null;
	$min = run(0, $y, $x, [], $d, $l);
	printf("\n=== Start [%02d:%02d] %s(%d) : Min=%d\n\n", $y, $x, $d, $l, $min);
#	ksort($cache); print_r($cache); var_dump(count($cache));
}

#start(12, 12, 'R', 1);	// 3
#start(12, 11, 'D', 2);	// 6
#start(11, 11, 'D', 1);	// 9
#start(10, 11, 'L', 1);	// 15

#start(10, 12, 'D', 3);	// 18
#start( 9, 12, 'D', 2);	// -21
#start( 8, 12, 'D', 1);	// -28
#start( 7, 12, 'R', 1);	// -31

#start( 7, 11, 'D', 3);	// -36
#start( 6, 11, 'D', 2);	// -42
#start( 5, 11, 'D', 1);	// -47 /vs 50
#start( 4, 11, 'R', 1);	// -50 /vs 53

#start( 4, 10, 'D', 2);	// -55
#start( 3, 10, 'D', 1);	// -63 vs 59
#start( 2, 10, 'R', 2);	// -65 vs 61
#start( 2,  9, 'R', 1);	// -68 vs 65

#start( 2,  8, 'D', 2);
#start( 1,  8, 'D', 1);
#start( 0,  8, 'R', 3);	// (-74)
#start( 0,  7, 'R', 2);
#start( 0,  6, 'R', 1);

#start( 0,  5, 'U', 1);
#start( 1,  5, 'R', 3);
#start( 1,  4, 'R', 2);
#start( 1,  3, 'R', 1);

#start( 1,  2, 'D', 1);
#start( 0,  2, 'R', 2);
#start( 0,  1, 'R', 1);
#start( 0,  0, 'R', 0);

#start( 0,  0, 'D', 0);
