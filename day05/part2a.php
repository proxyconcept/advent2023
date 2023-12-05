<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$seeds = array();
$maps = array();
$maps_to = array();

$cur_map = null;
foreach ($inputs as $line) {
	if (! $line) continue;
	if (preg_match('/^seeds:(.*)$/', $line, $m)) {
		$idx = 0;
		foreach (explode(' ', trim($m[1])) as $n) {
			if (! $idx) $idx = $n;
			else { $seeds[ $idx ] = $n; $idx = 0; }
		}
		continue;
	}
	if (preg_match('/^(.*)-to-(.*) map:$/', $line, $m)) {
		$cur_map = $m[1];
		$maps[$cur_map] = array();
		$maps_to[$cur_map] = $m[2];
		continue;
	}
	if (preg_match('/^(\d+) (\d+) (\d+)$/', $line, $m)) {
		$maps[$cur_map][] = $m;
		continue;
	}
	die($line);
}
#print_r($seeds); print_r($maps_to); print_r($maps); exit;

function find_in_map($map, $id) {
	global $maps, $maps_to;
	if ($map == 'location') return $id;
	$next = 0;
	foreach ($maps[$map] as $data) {
		list(, $dst, $src, $len) = $data;
		if ($id >= $src && $id < $src + $len) {
			$next = $id - $src + $dst;
			break;
		}
	}
#	printf("Map %12s : %010d => %010d\n", $map, $id, $next);
	return find_in_map($maps_to[$map], ($next > 0) ? $next : $id);
}

$min = null;
foreach ($seeds as $idx => $len) {
	for ($seed = $idx; $seed < $idx + $len; $seed++) {
		$location = find_in_map('seed', $seed);
		if ($min === null || $location < $min) $min = $location;
	}
}
printf("Min = %d\n", $min);
