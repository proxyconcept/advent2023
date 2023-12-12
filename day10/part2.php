<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$size_x = strlen($inputs[0]);
$size_y = count($inputs);
print_r(["Total_lenX"=>$size_x, "Total_lenY"=>$size_y]);

function find_start() {
	global $inputs, $size_x, $size_y;
	// recherche la ligne contenant le 'S' de départ
	foreach ($inputs as $y => $line) {
		if (($x = strpos($line, 'S')) !== false) break;
	}
	// étudie les cases voisines pour trouver les connexions
	$connected = array('t'=>"|7F", 'r'=>"-7J", 'b'=>"|LJ", 'l'=>"-LF");
	$neighbors = array('t'=>[$x,$y-1], 'r'=>[$x+1,$y], 'b'=>[$x,$y+1], 'l'=>[$x-1,$y]);
	$linked_to = '';
	foreach ($neighbors as $dir => $tile) {
		if ($tile[0] < 0 || $tile[1] < 0 || $tile[0] >= $size_x || $tile[1] >= $size_y) continue;
		$neighbor = $inputs[ $tile[1] ][ $tile[0] ];
		if (strpos($connected[$dir], $neighbor) !== false) $linked_to.= $dir;
	}
	// détermine la case du 'S' selon les connexions
	$conntypes = array('tr'=>'L', 'tb'=>'|', 'tl'=>'J', 'rb'=>'F', 'rl'=>'-', 'bl'=>'7');
	return [ $x, $y, $conntypes[$linked_to] ];
}

/*
 * ObI  ObI  OOO  OOO  OOO  IaO
 * OLa  O|I  OFb  a-b  a7O  bJO
 * OOO  OaI  OaI  III  IbO  OOO
 */
$path = array();
$prev = [-1, -1];
$tile = find_start();

// Suit le chemin (selon l'une des 2 connexions de la case, qui n'est pas la précédente)
while (true) {
	list($x, $y, $type) = $tile;
	if ($type === 'S') break;
	
	$linked = array();
	switch ($type) {
		case 'L' : $linked = array('ab' => [$x, $y-1], 'ba' => [$x+1, $y]); break;
		case '|' : $linked = array('ab' => [$x, $y-1], 'ba' => [$x, $y+1]); break;
		case 'F' : $linked = array('ab' => [$x+1, $y], 'ba' => [$x, $y+1]); break;
		case '-' : $linked = array('ab' => [$x+1, $y], 'ba' => [$x-1, $y]); break;
		case '7' : $linked = array('ab' => [$x, $y+1], 'ba' => [$x-1, $y]); break;
		case 'J' : $linked = array('ab' => [$x-1, $y], 'ba' => [$x, $y-1]); break;
	}
	foreach ($linked as $abba => $next) if (($next[0] != $prev[0]) || ($next[1] != $prev[1])) break;
	printf("[%03dx%03d] (%s) => [%03dx%03d] (%s)\n", $x, $y, $type, $next[0], $next[1], $abba);
	
	$path[$x.':'.$y] = [$x, $y, $abba, $type];
	$prev = $tile;
	$tile = array($next[0], $next[1], $inputs[ $next[1] ][ $next[0] ]);
}
printf("\n>>> Len=%d : Res=%d\n", count($path), count($path)/2);

// Recherche toutes les cases à l'intérieur du chemin (par propagation à partir du chemin)
function check($inverse = false) {
	global $inputs, $size_x, $size_y, $path, $res_in, $res_out;
	$res_in = $res_out = $todo = array();
	
	// Parcours le chemin pour trier les cases voisines (in/out) selon le sens de connexion (ab/ba)
	foreach ($path as $key => $tile) {
		list($x, $y, $abba, $type) = $tile;
		switch ($type) {
			case 'L' : $in = array([ 1,-1]); $out = array([-1,-1], [-1, 0], [-1, 1], [ 0, 1], [ 1, 1]); break;
			case '|' : $in = array([ 1,-1], [ 1, 0], [ 1, 1]); $out = array([-1,-1], [-1, 0], [-1, 1]); break;
			case 'F' : $in = array([ 1, 1]); $out = array([-1,-1], [ 0,-1], [ 1,-1], [-1, 0], [-1, 1]); break;
			case '-' : $in = array([-1, 1], [ 0, 1], [ 1, 1]); $out = array([-1,-1], [ 0,-1], [ 1,-1]); break;
			case '7' : $in = array([-1, 1]); $out = array([-1,-1], [ 0,-1], [ 1,-1], [ 1, 0], [ 1, 1]); break;
			case 'J' : $in = array([-1,-1]); $out = array([ 1,-1], [ 1, 0], [ 1, 1], [ 0, 1], [-1, 1]); break;
			default  : die("invalid type $type : " . json_encode($tile));
		}
		if (($abba === 'ba') xor $inverse) { $tmp = $in; $in = $out; $out = $tmp; unset($tmp); }
#		printf("%03dx%03d %d%s : %s / %s\n", $x, $y, $abba, $type, json_encode($in), json_encode($out));
		
		foreach ($out as $neighbor) {
			$nx = $x + $neighbor[0];
			$ny = $y + $neighbor[1];
			if ($nx < 0 || $ny < 0 || $nx >= $size_x || $ny >= $size_y) continue;
			$nk = $nx.':'.$ny;
			if (! isset($path[$nk])) $res_out[$nk] = [$nx, $ny];
		}
		foreach ($in as $neighbor) {
			$nx = $x + $neighbor[0];
			$ny = $y + $neighbor[1];
			if ($nx < 0 || $ny < 0 || $nx >= $size_x || $ny >= $size_y) return false;
			$nk = $nx.':'.$ny;
			if (isset($path[$nk])) continue;
			if (isset($res_in[$nk])) continue;
			if (isset($res_out[$nk])) return false;
			$res_in[$nk] = $todo[$nk] = [$nx, $ny];
		}
	}
	print_r(['out'=>count($res_out), 'in'=>count($res_in), 'todo'=>count($todo)]);
#	print_r(['out'=>implode(' ',array_keys($res_out)), 'in'=>implode(' ',array_keys($res_in)), 'todo'=>implode(' ',array_keys($todo))]);
	
	// Étude des cases voisines pour chaque case trouvée à l'intérieur (propagation tant que de nouvelles cases in sont trouvées)
	while (count($todo)) {
		$more = array();
		foreach ($todo as $key => $tile) {
			for ($y = $tile[1] - 1; $y <= $tile[1] + 1; $y++) {
				for ($x = $tile[0] - 1; $x <= $tile[0] + 1; $x++) {
					if ($x < 0 || $y < 0 || $x >= $size_x || $y >= $size_y) return false;
					$k = $x.':'.$y;
					if (isset($path[$k])) continue;
					if (isset($res_in[$k])) continue;
					if (isset($res_out[$k])) return false;
					$res_in[$k] = $more[$k] = [$x, $y];
				}
			}
		}
		$todo = $more;
		print_r(['out'=>count($res_out), 'in'=>count($res_in), 'todo'=>count($todo)]);
	}
#	print_r(['out'=>implode(' ',array_keys($res_out)), 'in'=>implode(' ',array_keys($res_in))]);
	return true;
}

$res_in = $res_out = array();
check() || check(true);
printf("\n>>> IN=%d / OUT=%d\n", count($res_in), count($res_out));

// Génération de la carte résolue (coloration des cases selon leur état path/in/out)

$fp = fopen("output.txt", 'w');
foreach ($inputs as $y => $line) {
	$out = '';
	foreach (str_split($line) as $x => $tile) {
		$pipe = str_replace(['L','|','F','-','7','J'], ['╚','║','╔','═','╗','╝'], $tile);
		$key = $x.':'.$y;
		if     (isset($res_out[$key])) $out.= "\033[1;41m" . $pipe . "\033[0;00m";
		elseif (isset($res_in[$key]))  $out.= "\033[1;42m" . $pipe . "\033[0;00m";
		elseif (isset($path[$key]))    $out.= "\033[1;33m" . $pipe . "\033[0;00m";
		else                           $out.= "\033[1;31m" . $pipe . "\033[0;00m";
	}
	fwrite($fp, $out."\n");
}
printf("\n>>> See result map : 'cat output.txt'\n");
