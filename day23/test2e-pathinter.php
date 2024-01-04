<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$nodes = make_nodes(find_connections());
unset($inputs);

// Liste des connexions possibles pour chaque case de chemin (voisins pratiquables)
function find_connections() {
	global $inputs;
	$cnx = array();
	$idx = 0;
	$x_max = strlen($inputs[0]) - 1;
	$y_max = count($inputs) - 1;
	foreach ($inputs as $y => $line) foreach (str_split($line) as $x => $c) if ($c !== '#') {
		$next = array();
		if (($x >      1) && ($inputs[$y][$x - 1] !== '#')) $next[] = [$y, $x - 1];
		if (($y >      1) && ($inputs[$y - 1][$x] !== '#')) $next[] = [$y - 1, $x];
		if (($x < $x_max) && ($inputs[$y][$x + 1] !== '#')) $next[] = [$y, $x + 1];
		if (($y < $y_max) && ($inputs[$y + 1][$x] !== '#')) $next[] = [$y + 1, $x];
		$cnx[$y][$x] = array('node' => $idx++, 'next' => $next);
	}
	return $cnx;
}

// Conversion des cases en liste de noeud avec leur liste de noeuds voisins
function make_nodes($cnx) {
	$nodes = array();
	foreach ($cnx as $y => $xxx) foreach ($xxx as $data) {
		$links = array();
		foreach ($data['next'] as $next) $links[] = $cnx[$next[0]][$next[1]]['node'];
		$nodes[ $data['node'] ] = $links;
	}
	return $nodes;
}

// Parcours les chemins possibles en suivant les connexions des noeuds
function walk($node = 0, $from = null, $done = [], $len = 1) {
	global $nodes, $fin, $max;
	
	// mise en cache des noeuds déjà parcourus (uniquement sur intersection)
	if (count($nodes[$node]) > 2) $done[] = $node;
	
	// récursion sur les connexions possibles pour la suite du chemin...
	foreach ($nodes[$node] as $link) {
		if ($link === $fin) {
			// soit noeud terminal (nouvelle longueur max ?)
			if ($len > $max) $max = $len;
#			printf(">>> Found path : %d\n", $len);
		}
		elseif (($link !== $from) && ! in_array($link, $done)) {
			// soit noeud non parcouru (ni précédent, ni intersection en cache)
			walk($link, $node, $done, $len + 1);
		}
	}
}

// Les noeuds de départ et d'arrivée sont forcément le premier et le dernier de la liste
$fin = max(array_keys($nodes)); // count($nodes) - 1
$max = 0;
walk();
printf("\nMax=%d\n\n", $max);
### time 42mn (memory_limit < 128M)
