<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

// Chargement des indications de direction d'un cycle (transformés en entier L=0 & R=1)
$directions = array();
foreach (str_split(array_shift($inputs)) as $dir) $directions[] = ($dir == 'L') ? 0 : 1;

// Chargement des chemins en notant les points de départ
$startnodes = $paths = array();
foreach ($inputs as $line) {
	if (! preg_match('/^([A-Z0-9]+)\s*=\s*\(([A-Z0-9]+),\s*([A-Z0-9]+)\)/', $line, $m)) continue;
	list(, $from, $left, $right) = $m;
	$paths[$from] = [ $left, $right ];
	if ($from[2] === 'A') $startnodes[] = $from;
}
print_r($startnodes);

// Variables pour la position de chaque fantome et leurs passages par les points d'arrivées
$node = $isok = array();
foreach ($startnodes as $ref) { $node[$ref] = $ref; $isok[$ref] = []; }

// Itérations pour analyse des cycles de chaque fantome (détermine leurs étapes)
$size = count($directions);
$step = 0;
while ($step < 100000) {
	$dir = $directions[ $step++ % $size ];
	foreach ($startnodes as $ref) {
		$node[$ref] = $paths[ $node[$ref] ][$dir];
		if ($node[$ref][2] === 'Z') $isok[$ref][] = $step;
	}
}

// Vérification de la régularité des cycles (intervalles constants entre les étapes)
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

// Recherche du 1er nombre d'étapes divisible par tous les cycles
$div = array_shift($loop);
for ($res = $div; true; $res+= $div) {
	foreach ($loop as $step) if ($res % $step) continue 2;
	break;
}
printf("\nRes=%d\n\n", $res);
