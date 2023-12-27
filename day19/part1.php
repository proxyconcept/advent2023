<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

// Application des règles d'un workflow sur une pièce
function run($name, $part) {
	global $wf;
	foreach ($wf[$name] as $rule) {
		// condition à tester ?
		switch ($rule['op']) {
			case '<' : if ($part[$rule['cat']] < $rule['val']) break; continue 2;
			case '>' : if ($part[$rule['cat']] > $rule['val']) break; continue 2;
		}
		// suite du workflow ?
		if (isset($rule['is'])) return $rule['is'];
		return run($rule['go'], $part);
	}
}

// Constitution de la liste des workflows (avec leurs règles)
$wf = array();
while ($line = array_shift($inputs)) {
	if ($line === '') break;
	
	if (! preg_match('/^([a-z]+)\{(.*)\}$/', $line, $m)) die("Invalid workflow : $line\n");
	list(, $name, $rules) = $m;
	$wf[$name] = array();
	
	foreach (explode(',', $rules) as $rule) {
		if (! preg_match('/^(([xmas])([<>])(\d+):)?(([a-z]+)|([AR]))$/', $rule, $m)) die("Invalid rule : $rule\n");
		$wf[$name][] = array('cat' => $m[2], 'op' => $m[3], 'val' => $m[4], 'go' => $m[6], 'is' => isset($m[7])?$m[7]:null);
	}
}
print_r($wf);

// Lancement des workflows sur chacune des pièces listées
$sum = 0;
foreach ($inputs as $line) {
	if (! preg_match('/^\{x=(\d+),m=(\d+),a=(\d+),s=(\d+)\}$/', $line, $m)) die("Invalid part : $line\n");
	$part = array('x' => $m[1], 'm' => $m[2], 'a' => $m[3], 's' => $m[4]);
	
	if (run("in", $part) === 'A') $sum+= array_sum($part);
}
printf("\nSum=%d\n\n", $sum);
