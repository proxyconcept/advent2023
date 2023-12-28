<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

// Constitution de la liste des workflows (avec leurs règles)
$wf = array();
while ($line = array_shift($inputs)) {
	if ($line === '') break;
	
	if (! preg_match('/^([a-z]+)\{(.*)\}$/', $line, $m)) die("Invalid workflow : $line\n");
	list(, $name, $rules) = $m;
	$wf[$name] = array();
	
	foreach (explode(',', $rules) as $rule) {
		if (! preg_match('/^(([xmas])([<>])(\d+):)?([a-z]+|[AR])$/', $rule, $m)) die("Invalid rule : $rule\n");
		$wf[$name][] = array('cat' => $m[2], 'op' => $m[3], 'val' => $m[4], 'go' => $m[5]);
	}
}
printf("\nWorkflows: %d\n", count($wf));

// Application des règles d'un workflow sur une pièce
function run($name, $parts) {
	global $wf;
	$ok_parts = array();
	foreach ($wf[$name] as $rule) {
		$parts1 = $parts2 = $parts;
		$cat = $rule['cat'];
		
		// condition à tester ? (ex "m<500")
		switch ($rule['op']) {
			case '<' :
				$parts1[$cat][1] = min($parts[$cat][1], $rule['val'] - 1);  // si test ok
				if ($parts1[ $rule['cat'] ][0] > $parts1[ $rule['cat'] ][1]) $parts1 = null;
				$parts2[$cat][0] = max($parts[$cat][0], $rule['val']);      // si test ko
				if ($parts2[ $rule['cat'] ][0] > $parts2[ $rule['cat'] ][1]) $parts2 = null;
				break;
			case '>' :
				$parts1[$cat][0] = max($parts[$cat][0], $rule['val'] + 1);  // si test ok
				if ($parts1[ $rule['cat'] ][0] > $parts1[ $rule['cat'] ][1]) $parts1 = null;
				$parts2[$cat][1] = min($parts[$cat][1], $rule['val']);      // si test ko
				if ($parts2[ $rule['cat'] ][0] > $parts2[ $rule['cat'] ][1]) $parts2 = null;
				break;
			default  :
				$parts2 = null;
				break;
		}
		
		// suite du workflow : si test ok
		if ($parts1) {
			if     ($rule['go'] === 'A') {
				$ok_parts[] = $parts1;
			}
			elseif ($rule['go'] !== 'R') {
				foreach (run($rule['go'], $parts1) as $ok) $ok_parts[] = $ok;
			}
		}
		// suite du workflow : si test ko
		if (! $parts2) break;
		$parts = $parts2;
	}
	return $ok_parts;
}

// Lancement des workflows sur chacune des pièces listées
$parts = array(
	'x' => [ 1, 4000 ],
	'm' => [ 1, 4000 ],
	'a' => [ 1, 4000 ],
	's' => [ 1, 4000 ],
);
$ok_parts = run("in", $parts);
print_r($ok_parts);

//
$count = 0;
foreach ($ok_parts as $part) {
	$lx = $part['x'][1] - $part['x'][0] + 1;
	$lm = $part['m'][1] - $part['m'][0] + 1;
	$la = $part['a'][1] - $part['a'][0] + 1;
	$ls = $part['s'][1] - $part['s'][0] + 1;
	$count+= $lx * $lm * $la * $ls;
}
printf("\nCount=%d\n\n", $count);
