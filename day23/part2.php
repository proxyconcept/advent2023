<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

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
	global $fin;
	$nodes = array();
	foreach ($cnx as $y => $xxx) foreach ($xxx as $data) {
		$links = array();
		foreach ($data['next'] as $next) $links[] = $cnx[$next[0]][$next[1]]['node'];
		$nodes[ $data['node'] ] = $links;
	}
	$fin = max(array_keys($nodes)); // count($nodes) - 1
	return $nodes;
}

// Conversion de la liste de noeuds en graph des intersections (pondérées par les distances)
function make_graph($nodes) {
	global $fin;
	$graph = array();
	
	foreach ($nodes as $node => $links) {
		// uniquement pour les intersections + départ & arrivée
		if (($node !== 0) && ($node !== $fin) && (count($links) < 3)) continue;
		$graph[$node] = array();
		
		// calcul pour chaque connexion la distance vers l'intersection suivante
		foreach ($links as $next) {
			$from = $node;
			$dist = 1;
			// recherche la prochaine intersection (ou départ/arrivée)
			while ($next !== 0 && $next !== $fin && count($nodes[$next]) < 3) {
				foreach ($nodes[$next] as $link) if ($link !== $from) break;
				// note: sachant qu'il n'y a aucune impasse (hormis départ/arrivée)
				$from = $next; $next = $link; $dist++;
			}
			$graph[$node][$next] = $dist;
		}
	}
	return $graph;
}

// Parcours les chemins possibles en suivant les connexions du graph
function walk($node = 0, $from = null, $done = [], $len = 0) {
	global $graph, $fin, $max;
	
	// fin du parcours si noeud terminal (nouvelle longueur max ?)
	if ($node === $fin) return $max = max($max, $len);
	
	// fin du parcours si intersection déjà parcourue, sinon mise en cache du chemin en cours
	if (in_array($node, $done)) return;
	$done[] = $node;
	
	// récursion sur les connexions possibles pour la suite (hors marche-arrière)
	foreach ($graph[$node] as $next => $dist) {
		if ($next !== $from) walk($next, $node, $done, $len + $dist);
	}
}

$graph = make_graph(make_nodes(find_connections()));
print_r(array_map('json_encode', $graph));

$timer = microtime(true);
$fin = max(array_keys($graph));
$max = 0; walk();
printf("\nMax=%d (%.3fs) : %s\n\n", $max, microtime(true) - $timer, basename(__FILE__));
### time 12s (memory_limit < 128M)
