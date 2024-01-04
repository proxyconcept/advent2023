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
		if (($x >      0) && ($inputs[$y][$x - 1] !== '#')) $next[] = [$y, $x - 1];
		if (($y >      0) && ($inputs[$y - 1][$x] !== '#')) $next[] = [$y - 1, $x];
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
function walk($node = 0, $from = null, $done = [], $len = 0) {
	global $nodes, $fin, $max;
	
	// recherche de la prochaine intersection (avance sans récursion)
	while (count($nodes[$node]) < 3) {
		// fin du parcours si noeud terminal (nouvelle longueur max ?)
		if ($node === $fin) return $max = max($max, $len);
		// recherche du noeud suivant (en éliminant le noeud précédent)
		foreach ($nodes[$node] as $link) if ($link !== $from) break;
		// note: sachant qu'il n'y a aucune impasse (hormis départ/arrivée)
		$from = $node; $node = $link; $len++;
	}
	
	// fin du parcours si intersection déjà parcourue, sinon mise en cache du chemin en cours
	if (in_array($node, $done)) return;
	$done[] = $node;
	
	// récursion sur les connexions possibles pour la suite (hors marche-arrière)
	foreach ($nodes[$node] as $link) if ($link !== $from) walk($link, $node, $done, $len + 1);
}

// Les noeuds de départ et d'arrivée sont forcément le premier et le dernier de la liste
$fin = max(array_keys($nodes)); // count($nodes) - 1
$max = 0;
walk();
printf("\nMax=%d\n\n", $max);
### time 16mn (memory_limit < 128M)
