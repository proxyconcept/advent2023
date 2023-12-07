<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

function find_strength($hand) {
	$counters = array();
	foreach (str_split($hand) as $card) {
		if (! isset($counters[$card])) $counters[$card] = 0;
		$counters[$card]++;
	}
	if (isset($counters['J'])) {
		$jokers = $counters['J'];
		unset($counters['J']);
		if (empty($counters)) $counters['J'] = 0;
		rsort($counters);
		$counters[0]+= $jokers;
	}
	// 6 = five of kind  : 5
	// 5 = four of kind  : 4 1
	// 4 = full house    : 3 2
	// 3 = three of kind : 3 1 1
	// 2 = two pairs     : 2 2 1
	// 1 = one pair      : 2 1 1 1
	// 0 = high card     : 1 1 1 1 1
	switch (count($counters)) {
		case 5 : return 0;
		case 4 : return 1;
		case 3 : return in_array(2, $counters) ? 2 : 3;
		case 2 : return in_array(3, $counters) ? 4 : 5;
		case 1 : return 6;
	}
}

$hands = array();
foreach ($inputs as $line) {
	list($hand, $bid) = explode(' ', $line);
	$hands[] = array(
		'hand' => $hand,
		'bid'  => intval($bid),
		'strength' => find_strength($hand),
	);
}
#print_r($hands);

usort($hands, function($a, $b){
	$s = $a['strength'] - $b['strength'];
	if ($s) return $s;
	$aa = strtr($a['hand'], "J23456789TQKA", "BCDEFGHILMNOP");
	$bb = strtr($b['hand'], "J23456789TQKA", "BCDEFGHILMNOP");
	return $aa <=> $bb;
});
#print_r($hands);

$total = $rank = 0;
foreach ($hands as $hand) $total+= $hand['bid'] * ++$rank;
printf("\nTotal winnings: %d\n\n", $total);
