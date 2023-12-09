<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

function calc_sequences($serie) {
	$seqs = array($serie);
	do {
		$seq = array();
		$prev = $diff = $todo = null;
		foreach ($serie as $cur) {
			if ($prev !== null) $seq[] = $diff = $cur - $prev;
			if ($diff) $todo = true;
			$prev = $cur;
		}
		$seqs[] = $serie = $seq;
	} while ($todo);
	
	$new = 0;
	foreach (array_reverse($seqs) as $seq) $new = end($seq) + $new;
	return $new;
}

$sum = 0;
foreach ($inputs as $line) $sum+= calc_sequences(explode(' ', $line));
printf("\nSum=%d\n\n", $sum);
