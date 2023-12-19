<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

### Récursion pour parcourir toutes les cases de la ligne en testant les choix possibles
function count_arrangements($row, $idx = 0, $pos = 0) {
	global $conditions, $cache;
	// utilisation du résultat en cache si cette branche a déjà été évaluée
	if (isset($cache[$pos.':'.$idx])) return $cache[$pos.':'.$idx];
	
	$size = strlen($row) - $pos;
	$ngrp = count($conditions) - $idx;
	
	// verdict si nombre de cases à parcourir épuisé (selon groupes restants ou non)
	if ($size <= 0) return ($ngrp) ? 0 : 1;
	// verdict si aucun groupe restant (il ne dois plus y avoir de case avec un '#')
	if (! $ngrp) return (strpos(substr($row, $pos), '#') === false);
	// verdict si nombre de cases à parcourir insuffisant pour les groupes restants
	if ($size < $conditions[$idx][1]) return 0;
	
	$nb = 0;
	switch ($row[$pos]) {
		case '?' :
			// vérification si la case est '.' + si la case est '#'
			$nb = count_arrangements($row, $idx, $pos + 1);
		case '#' :
			// vérification que le prochain groupe peut être contenu à partir de cette case
			$group = $conditions[$idx][0];                                      // prise en compte du prochain groupe...
			if (strpos(substr($row, $pos, $group), '.') !== false) break;       // case ne pouvant être dans le groupe !
			if ($group === $size) { $nb++; break; }                             // groupe contenant les dernières cases
			if ($row[$pos + $group] === '#') break;                             // case suivante prolongeant le groupe !
			$nb+= count_arrangements($row, $idx + 1, $pos + $group + 1);        // poursuite après la case séparateur...
			break;
		case '.' :
			// vérification pour les groupes restants à poursuivre à partir de la prochaine case
			$nb = count_arrangements($row, $idx, $pos + 1);
			break;
		default  : die("Wrong tile at pos $pos : $row");
	}
	// retourne le résultat pour cette branche (tout en le mettant en cache)
	return $cache[$pos.':'.$idx] = $nb;
}

$sum = 0;
foreach ($inputs as $line) {
	list($row1, $grp1) = explode(' ', $line);
	$row = $row1 . '?' . $row1 . '?' . $row1 . '?' . $row1 . '?' . $row1;
	$grp = $grp1 . ',' . $grp1 . ',' . $grp1 . ',' . $grp1 . ',' . $grp1;
	
	$conditions = array();
	$min_length = -1;
	foreach (array_reverse(array_map('intval', explode(',', $grp))) as $c) {
		$min_length+= $c + 1;
		$conditions[] = [ $c, $min_length ];
	}
	$conditions = array_reverse($conditions);
#	print_r($conditions);
	
	$cache = array();
	$res = count_arrangements($row);
	printf(">>>%120s |%60s =>%15d\n", $row, $grp, $res);
	$sum+= $res;
}
printf("\nSum=%d\n\n", $sum);
### input1 => 0.02s
