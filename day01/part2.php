<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

// Liste des nombres à retrouver et construction du motif de recherche (regexp)
$digits = array('one'=>1,'two'=>2,'three'=>3,'four'=>4,'five'=>5,'six'=>6,'seven'=>7,'eight'=>8,'nine'=>9);
#$findme = join('|', array_keys($digits)) . '|' . join('|', array_values($digits));
$findme = join('|', array_merge(array_keys($digits), array_values($digits)));
var_dump($findme);

function calc($str) {
	global $digits, $findme;
	
	// ERREUR: une ligne terminant en "oneight" matchera sur "one" et pas sur "eight"
#	preg_match_all('/'.$findme.'/', $str, $m) or die($str);
	
	// ERREUR: récupère bien le premier et le dernier mais ne marche pas si un seul nombre
#	if (! preg_match('/('.$findme.').*('.$findme.')/', $str, $m)) die($str);
#	$d1 = reset($m[0]); if (! ctype_digit($d1)) $d1 = $digits[$d1];
#	$d2 = end($m[0]);   if (! ctype_digit($d2)) $d2 = $digits[$d2];
	
	// OKAY: une regexp pour le premier & une autre pour le dernier (peut-être le même)
	if (! preg_match('/('.$findme.')/', $str, $m)) die($str);
	$d1 = ctype_digit($m[1]) ? $m[1] : $digits[$m[1]];
	if (! preg_match('/.*('.$findme.')/', $str, $m)) die($str);
	$d2 = ctype_digit($m[1]) ? $m[1] : $digits[$m[1]];
	
	printf("[%s] [%s] %s\n", $d1, $d2, $str);
	return $d1 . $d2;
}

$sum = 0;
foreach ($inputs as $line) $sum+= calc($line);
printf("SUM=%d\n", $sum);
