<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

$width = strlen($inputs[0]);
$height = count($inputs);

// Additionne les poids au nord en traitant colonne par colonne...
$sum = 0;
for ($x = 0; $x < $width; $x++) {
	$pos = 0; // position courante pour les pierres rondes
	foreach ($inputs as $y => $line) {
		switch ($line[$x]) {
			case '.' : break;
			case '#' : $pos = $y + 1; break;
			case 'O' : $sum+= $height - $pos++; break;
			default : die("Unknown tile $x/$y on line: ".$line);
		}
	}
}
printf("\nSum=%d\n\n", $sum);
