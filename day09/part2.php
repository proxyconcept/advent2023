<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

function calc_sequence($serie) {
	$seq = array();
	$prev = null;
	foreach ($serie as $n) {
		if ($prev !== null) $seq[] = $n - $prev;
		$prev = $n;
	}
	return $seq;
}

function calc_sequences($serie) {
	$seqs = array($serie);
	while (true) {
		$serie = calc_sequence($serie);
		$seqs[] = $serie;
		
		$all_zero = true;
		foreach ($serie as $n) if ($n != 0) { $all_zero = false; break; }
		if ($all_zero) break;
	}
	return $seqs;
}

function prev_sequences($seqs) {
	$new = 0;
	foreach (array_reverse($seqs) as $seq) $new = reset($seq) - $new;
	return $new;
}

$sum = 0;
foreach ($inputs as $line) {
	$serie = explode(' ', $line);
	$nextval = prev_sequences(calc_sequences($serie));
	printf("%s : %d\n", join(' ',$serie), $nextval);
	$sum+= $nextval;
}
printf("\nSum=%d\n\n", $sum);
