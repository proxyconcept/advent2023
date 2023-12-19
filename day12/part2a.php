<?php
$inputs = file('input1.txt', FILE_IGNORE_NEW_LINES);

### Récursion pour parcourir toutes les cases de la ligne en testant les choix possibles
function count_arrangements($row, $groups, $pos = 0) {
	$size = strlen($row) - $pos;
	$ngrp = count($groups);
	
	// verdict si nombre de cases à parcourir épuisé (selon groupes restants ou non)
	if ($size <= 0) return ($ngrp) ? 0 : 1;
	// verdict si aucun groupe restant (il ne dois plus y avoir de case avec un '#')
	if (! $ngrp) return (strpos(substr($row, $pos), '#') === false);
	// verdict si nombre de cases à parcourir insuffisant pour les groupes restants
	if ($size < array_sum($groups) + $ngrp - 1) return 0;
	
	$nb = 0;
	switch ($row[$pos]) {
		case '?' :
			// vérification si la case est '.' + si la case est '#'
			$nb = count_arrangements($row, $groups, $pos + 1);
		case '#' :
			// vérification que le prochain groupe peut être contenu à partir de cette case
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
foreach ($inputs as $line) {
	list($row1, $grp1) = explode(' ', $line);
	$row = $row1 . '?' . $row1 . '?' . $row1 . '?' . $row1 . '?' . $row1;
	$grp = $grp1 . ',' . $grp1 . ',' . $grp1 . ',' . $grp1 . ',' . $grp1;
	$res = count_arrangements($row, array_map('intval', explode(',', $grp)));
	printf(">>>%120s |%60s =>%15d\n", $row, $grp, $res);
	$sum+= $res;
}
printf("\nSum=%d\n\n", $sum);
### input1 => 9.5s
