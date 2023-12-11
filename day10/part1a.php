<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$size_x = strlen($inputs[0]);
$size_y = count($inputs);
print_r([$size_x, $size_y]);

// recherche la ligne contenant le 'S' de départ
function find_start() {
	global $inputs;
	foreach ($inputs as $y => $line) {
		if (($x = strpos($line, 'S')) !== false) return [$x,$y];
	}
}

// étudie les cases voisines pour deviner la case du 'S'
function guess_tile($pos) {
	global $inputs, $size_x, $size_y;
	list($x, $y) = $pos;
	$connected = array('t'=>"|7F", 'b'=>"|LJ", 'r'=>"-7J", 'l'=>"-LF");
	$neighbors = array('t'=>[$x,$y-1], 'r'=>[$x+1,$y], 'b'=>[$x,$y+1], 'l'=>[$x-1,$y]);
	$linked_to = '';
	foreach ($neighbors as $dir => $tile) {
		if ($tile[0] < 0 || $tile[1] < 0 || $tile[0] >= $size_x || $tile[1] >= $size_y) continue;
		$neighbor = $inputs[ $tile[1] ][ $tile[0] ];
		if (strpos($connected[$dir], $neighbor) !== false) $linked_to.= $dir;
	}
	switch ($linked_to) {
		case 'tr' : return 'L';
		case 'tb' : return '|';
		case 'tl' : return 'J';
		case 'rb' : return 'F';
		case 'rl' : return '-';
		case 'bl' : return '7';
	}
}

$start_pos = find_start();
$start_dir = guess_tile($start_pos);
print_r($start_pos); var_dump($start_dir);

function next_tiles($x, $y, $t) {
	global $inputs, $path_list;
	$path_list[] = "$x:$y";
	$linked = array();
	switch ($t) {
		case 'L' : $linked[] = [$x, $y-1]; $linked[] = [$x+1, $y]; break;
		case '|' : $linked[] = [$x, $y-1]; $linked[] = [$x, $y+1]; break;
		case 'J' : $linked[] = [$x, $y-1]; $linked[] = [$x-1, $y]; break;
		case 'F' : $linked[] = [$x+1, $y]; $linked[] = [$x, $y+1]; break;
		case '-' : $linked[] = [$x-1, $y]; $linked[] = [$x+1, $y]; break;
		case '7' : $linked[] = [$x-1, $y]; $linked[] = [$x, $y+1]; break;
	}
	foreach ($linked as $next) {
		if (in_array($next[0].':'.$next[1], $path_list)) continue;
		return next_tiles($next[0], $next[1], $inputs[ $next[1] ][ $next[0] ]);
	}
}

$path_list = array();
next_tiles($start_pos[0], $start_pos[1], $start_dir);
print_r($path_list);

printf("\nRes=%d\n\n", count($path_list)/2);
