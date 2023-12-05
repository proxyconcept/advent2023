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
		$maps[$cur_map][] = array('src' => $m[2], 'dst' => $m[1], 'fin' => $m[2] + $m[3]);
		continue;
	}
	die($line);
}
#print_r($seeds); print_r($maps_to); print_r($maps);
foreach ($maps as $map => $data) usort($maps[$map], function($a, $b){ return $a['fin'] > $b['fin']; });
#print_r($maps);exit;

function find_in_map($map, $id) {
	global $maps, $maps_to;
	if ($map == 'location') return $id;
	$next = 0;
	foreach ($maps[$map] as $data) {
		if ($id < $data['src']) break;
		if ($id >= $data['fin']) continue;
		$next = $id - $data['src'] + $data['dst'];
		break;
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
