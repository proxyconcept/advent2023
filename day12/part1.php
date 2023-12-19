<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

### Récursion pour parcourir toutes les cases de la ligne en testant les choix possibles
function count_arrangements($row, $groups, $pos = 0) {
	$size = strlen($row) - $pos;
	// verdict si nombre de cases à parcourir épuisé (selon groupes restants ou non)
	if ($size <= 0) return count($groups) ? 0 : 1;
	// verdict si nombre de cases à parcourir insuffisant pour les groupes restants
	if ($size < array_sum($groups) + count($groups) - 1) return 0;
	
	$nb = 0;
	switch ($row[$pos]) {
		case '?' :
			// vérification si la case est '.' + si la case est '#'
			$nb = count_arrangements($row, $groups, $pos + 1);
		case '#' :
			// vérification que le prochain groupe peut être contenu à partir de cette case
			if (! $groups) return $nb;                                          // aucun groupe restant !
			$group = array_shift($groups);                                      // prise en compte du prochain groupe...
			if (strpos(substr($row, $pos, $group), '.') !== false) return $nb;  // case ne pouvant être dans le groupe !
			if ($group === $size) return $nb + 1;                               // groupe contenant les dernières cases
			if ($row[$pos + $group] === '#') return $nb;                        // case suivante prolongeant le groupe !
			return $nb + count_arrangements($row, $groups, $pos + $group + 1);  // poursuite après la case séparateur...
		case '.' :
			// vérification pour les groupes restants à poursuivre à partir de la prochaine case
			return count_arrangements($row, $groups, $pos + 1);
		default  : die("Wrong tile at pos $pos : $row");
	}
}

$sum = 0;
foreach ($inputs as $i => $line) {
	list($row, $groups) = explode(' ', $line);
	$res = count_arrangements($row, array_map('intval', explode(',', $groups)));
	printf(">>> %25s | %15s => %3d\n", $row, $groups, $res);
	$sum+= $res;
}
printf("\nSum=%d\n\n", $sum);
