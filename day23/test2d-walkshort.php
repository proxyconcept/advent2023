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
function walk($node, $path = []) {
	global $nodes, $node_fin, $len_max;
	
	// mise en cache des noeuds déjà parcourus
	$path[] = $node;
	
	// possibilités pour la suite du chemin...
	foreach ($nodes[$node] as $link) {
		
		// soit case terminale (nouvelle longueur max ?)
		if ($link === $node_fin) {
			$l = count($path);
			if ($l > $len_max) $len_max = $l;
			printf(">>> Found path : %d\n", $l);
		}
		
		// soit case non parcourue
		elseif (! in_array($link, $path)) walk($link, $path);
	}
}

// Raccourcissement du chemin complet à mémoriser en commençant à la première intersection
$node_deb = $node_old = $len_deb = 0;
while (true) {
	$next = array();
	foreach ($nodes[$node_deb] as $link) if ($link !== $node_old) $next[] = $link;
	if (count($next) > 1) break;
	$node_old = $node_deb;
	$node_deb = $next[0];
	$len_deb++;
}

// Les noeuds de départ et d'arrivée sont forcément le premier et le dernier de la liste
printf("\nStart at node '%d' (length %d)...\n", $node_deb, $len_deb);
$node_fin = max(array_keys($nodes)); // count($nodes) - 1
$len_max = 0;
walk($node_deb);

printf("\nLength=%d\n\n", $len_deb + $len_max);
### /!\ memory_limit > 1G & timers ???...
