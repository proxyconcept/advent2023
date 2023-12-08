<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$directions = str_split(array_shift($inputs));

$paths = array();
foreach ($inputs as $line) {
	if (! $line) continue;
	if (! preg_match('/^([A-Z0-9]+)\s*=\s*\(([A-Z0-9]+),\s*([A-Z0-9]+)\)/', $line, $m)) die('ERR:'.$line);
	list(, $from, $left, $right) = $m;
	
	$paths[$from] = ['L' => $left, 'R' => $right];
}
#print_r($paths);

$dirs = $directions;
$step = 0;
$node = 'AAA';
while ($node != 'ZZZ') {
	if (empty($dirs)) $dirs = $directions;
	$dir = array_shift($dirs);
	$step++;
	
	$node = $paths[$node][$dir];
}
printf("\nNb step=%d\n\n", $step);
