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

$node = array();
foreach ($startnodes as $ref) $node[$ref] = $ref;
foreach ($directions as $k => $dir) $directions[$k] = ($dir == 'L') ? 0 : 1;

$size = count($directions);
$step = 0;
while (true) {
	$dir = $directions[ $step++ % $size ];
	
	$todo = false;
	foreach ($startnodes as $ref) {
		$node[$ref] = $paths[ $node[$ref] ][$dir];
		if ((! $todo) && ($node[$ref][2] !== 'Z')) $todo = true;
	}
	if (! $todo) break;
}
printf("\nNb step=%d\n\n", $step);
