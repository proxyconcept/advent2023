<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$directions = str_split(array_shift($inputs));

$startnodes = $paths = array();
foreach ($inputs as $line) {
	if (! $line) continue;
	if (! preg_match('/^([A-Z0-9]+)\s*=\s*\(([A-Z0-9]+),\s*([A-Z0-9]+)\)/', $line, $m)) die('ERR:'.$line);
	list(, $from, $left, $right) = $m;
	
	$paths[$from] = ['L' => $left, 'R' => $right];
	if ($from[2] === 'A') $startnodes[] = $from;
}
#print_r($paths);
print_r($startnodes);

$node = array();
foreach ($startnodes as $ref) $node[$ref] = $ref;

$dirs = $directions;
$step = 0;
while (true) {
	if (empty($dirs)) $dirs = $directions;
	$dir = array_shift($dirs);
	$step++;
	
	$todo = 0;
	foreach ($startnodes as $ref) {
		$node[$ref] = $paths[ $node[$ref] ][$dir];
		if ($node[$ref][2] !== 'Z') $todo++;
	}
	if (! $todo) break;
}
printf("\nNb step=%d\n\n", $step);
