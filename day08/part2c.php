<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$directions = str_split(array_shift($inputs));

$startnodes = $paths = array();
foreach ($inputs as $line) {
	if (! $line) continue;
	if (! preg_match('/^([A-Z0-9]+)\s*=\s*\(([A-Z0-9]+),\s*([A-Z0-9]+)\)/', $line, $m)) die('ERR:'.$line);
	list(, $from, $left, $right) = $m;
	
	$paths[$from] = [ $left, $right ];
	if ($from[2] === 'A') $startnodes[] = $from;
}
#print_r($paths);
print_r($startnodes);

$node = $isok = array();
foreach ($startnodes as $ref) { $node[$ref] = $ref; $isok[$ref] = []; }
foreach ($directions as $k => $dir) $directions[$k] = ($dir == 'L') ? 0 : 1;

### Itérations pour analyse des cycles
$size = count($directions);
$step = 0;
while ($step < 100000) {
	$dir = $directions[ $step++ % $size ];
	foreach ($startnodes as $ref) {
		$node[$ref] = $paths[ $node[$ref] ][$dir];
		if ($node[$ref][2] === 'Z') $isok[$ref][] = $step;
	}
}

### Vérification de la régularité des cycles
$loop = array();
foreach ($startnodes as $ref) {
	$prev = 0;
	foreach ($isok[$ref] as $step) {
		$dist = $step - $prev;
		if (! isset($loop[$ref])) $loop[$ref] = $dist;
		elseif ($loop[$ref] != $dist) die("[$ref] different dist");
		$prev = $step;
	}
}
arsort($loop);
print_r($loop);

### Recherche du 1er nombre d'étapes divisible par tous les cycles
$res = $div = array_shift($loop);
while (true) {
	$res+= $div;
	foreach ($loop as $step) if ($res % $step) continue 2;
	break;
}
printf("\nRes=%d\n\n", $res);
