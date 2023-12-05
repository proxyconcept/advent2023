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
			if (! $idx) $idx = intval($n);
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
		$maps[$cur_map][] = array(
			'src' => intval($m[2]),
			'dst' => intval($m[1]),
			'fin' => intval($m[2]) + intval($m[3]),
			'dif' => intval($m[1]) - intval($m[2])
		);
		continue;
	}
	die($line);
}
foreach ($maps as $map => $data) usort($maps[$map], function($a, $b){ return $a['fin'] > $b['fin']; });
#print_r($seeds); print_r($maps_to); print_r($maps);exit;

function find_in_map($map, $id) {
	global $maps, $maps_to;
	if ($map == 'location') return $id;
	$next = 0;
	foreach ($maps[$map] as $data) {
		if ($id < $data['src']) break;
		if ($id >= $data['fin']) continue;
		$next = $id + $data['dif']; break;
	}
	if ($next === 0) $next = $id;
	return find_in_map($maps_to[$map], $next);
}

$min = 5555555555;
foreach ($seeds as $idx => $len) {
	for ($seed = $idx; $seed < $idx + $len; $seed++) {
		$location = find_in_map('seed', $seed);
		if ($location < $min) $min = $location;
	}
}
printf("Min = %d\n", $min);
