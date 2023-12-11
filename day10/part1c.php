<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$size_x = strlen($inputs[0]);
$size_y = count($inputs);
print_r(["Total_lenX"=>$size_x, "Total_lenY"=>$size_y]);

// recherche la ligne contenant le 'S' de départ
function find_start() {
	global $inputs;
	foreach ($inputs as $y => $line) {
		if (($x = strpos($line, 'S')) !== false) return [$x,$y];
	}
}

// étudie les cases voisines pour deviner la case du 'S'
function next_start($x, $y) {
	global $inputs, $size_x, $size_y;
	$connected = array('t'=>"|7F", 'b'=>"|LJ", 'r'=>"-7J", 'l'=>"-LF");
	$neighbors = array('t'=>[$x,$y-1], 'r'=>[$x+1,$y], 'b'=>[$x,$y+1], 'l'=>[$x-1,$y]);
	foreach ($neighbors as $dir => $tile) {
		if ($tile[0] < 0 || $tile[1] < 0 || $tile[0] >= $size_x || $tile[1] >= $size_y) continue;
		$neighbor = $inputs[ $tile[1] ][ $tile[0] ];
		if (strpos($connected[$dir], $neighbor) !== false) return $tile;
	}
}

$prev = find_start();
$tile = next_start($prev[0], $prev[1]);
print_r([ "start"=>$prev, "next"=>$tile ]);

// suit le chemin (selon l'une des 2 connexions de la case, qui n'est pas la précédente)
$path = array();
while (true) {
	$path[] = $tile;
	list($x, $y) = $tile;
	$linked = array();
	switch ($inputs[ $y ][ $x ]) {
		case 'S' : break 2;
		case 'L' : $linked[] = [$x, $y-1]; $linked[] = [$x+1, $y]; break;
		case '|' : $linked[] = [$x, $y-1]; $linked[] = [$x, $y+1]; break;
		case 'J' : $linked[] = [$x, $y-1]; $linked[] = [$x-1, $y]; break;
		case 'F' : $linked[] = [$x+1, $y]; $linked[] = [$x, $y+1]; break;
		case '-' : $linked[] = [$x-1, $y]; $linked[] = [$x+1, $y]; break;
		case '7' : $linked[] = [$x-1, $y]; $linked[] = [$x, $y+1]; break;
	}
	foreach ($linked as $next) if (($next[0] != $prev[0]) || ($next[1] != $prev[1])) break;
	$prev = $tile;
	$tile = $next;
}
#print_r($path);

printf("\n>>> Len=%d : Res=%d\n", count($path), count($path)/2);
