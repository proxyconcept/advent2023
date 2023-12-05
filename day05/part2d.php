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
			'dst' => intval($m[1]),
			'src' => intval($m[2]),
			'len' => intval($m[3]),
		);
		continue;
	}
	die($line);
}
foreach ($maps as $map => $data) usort($maps[$map], function($a, $b){ return ($a['src'] > $b['src']) ? 1 : -1; });
#print_r($seeds); print_r($maps_to); print_r($maps);exit;

$mapz = array();
foreach ($maps as $map => $data) {
	$mapz[ $map ] = array();
	$limit = 0;
	foreach ($data as $d) {
		if ($d['src'] > $limit) $mapz[ $map ][ $d['src'] ] = 0;
		$limit = $d['src'] + $d['len'];
		$mapz[ $map ][ $limit ] = $d['dst'] - $d['src'];
	}
}
print_r($mapz);

function find_in_map($map, $id) {
	global $mapz, $maps_to;
	if ($map === 'location') return $id;
	foreach ($mapz[$map] as $limit => $delta) {
		if ($id < $limit) { $id+= $delta; break; }
	}
	return find_in_map($maps_to[$map], $id);
}

$min = 5555555555;
foreach ($seeds as $idx => $len) {
	for ($seed = $idx; $seed < $idx + $len; $seed++) {
		$location = find_in_map('seed', $seed);
		if ($location < $min) $min = $location;
	}
}
printf("Min = %d\n", $min);
