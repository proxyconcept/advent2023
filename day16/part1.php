<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$width = strlen($inputs[0]);
$height = count($inputs);

$delta = array(
	'U' => [ -1,  0 ],
	'D' => [ +1,  0 ],
	'L' => [  0, -1 ],
	'R' => [  0, +1 ],
);
$energized = array();

function run($y, $x, $d) {
	global $energized, $inputs, $width, $height, $delta;
	
	// application du déplacement (fin de parcours si hors limite)
	list($dy, $dx) = $delta[$d];
	$y+= $dy; if (($y < 0) || ($y >= $height)) return;
	$x+= $dx; if (($x < 0) || ($x >= $width)) return;
	
	// ajout de la case illuminée (fin de parcours si déjà parcouru dans le même sens)
#	printf("[%s] %02d x %02d\n", $d, $x, $y);
	if (! array_key_exists($x.':'.$y, $energized)) $energized[$x.':'.$y] = '';
	elseif (strpos($energized[$x.':'.$y], $d) !== false) return;
	$energized[$x.':'.$y].= $d;
	
	// appel récursif pour suite du parcours selon la direction et la case courante
	switch ($d) {
		case 'R': switch ($inputs[$y][$x]) {
			case '.':  return run($y, $x, 'R');
			case '-':  return run($y, $x, 'R');
			case '/':  return run($y, $x, 'U');
			case '\\': return run($y, $x, 'D');
			case '|':  return run($y, $x, 'U') + run($y, $x, 'D');
		}
		case 'L': switch ($inputs[$y][$x]) {
			case '.':  return run($y, $x, 'L');
			case '-':  return run($y, $x, 'L');
			case '/':  return run($y, $x, 'D');
			case '\\': return run($y, $x, 'U');
			case '|':  return run($y, $x, 'D') + run($y, $x, 'U');
		}
		case 'D': switch ($inputs[$y][$x]) {
			case '.':  return run($y, $x, 'D');
			case '|':  return run($y, $x, 'D');
			case '/':  return run($y, $x, 'L');
			case '\\': return run($y, $x, 'R');
			case '-':  return run($y, $x, 'L') + run($y, $x, 'R');
		}
		case 'U': switch ($inputs[$y][$x]) {
			case '.':  return run($y, $x, 'U');
			case '|':  return run($y, $x, 'U');
			case '/':  return run($y, $x, 'R');
			case '\\': return run($y, $x, 'L');
			case '-':  return run($y, $x, 'R') + run($y, $x, 'L');
		}
	}
}

run(0, -1, 'R');
printf("\nSum=%d\n\n", count($energized));
