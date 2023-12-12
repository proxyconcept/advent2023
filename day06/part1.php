<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

list(, $time) = explode(':', $inputs[0], 2);
list(, $dist) = explode(':', $inputs[1], 2);

$times = preg_split('/\s+/', trim($time));
$dists = preg_split('/\s+/', trim($dist));
print_r(["times"=>$times, "dists"=>$dists]);

### Exemple :
###   temps max = 10ms
###   - 1ms load / 9ms move (@ 1mm/ms) => 9mm
###   - 2ms load / 8ms move (@ 2mm/ms) => 16mm
###   - 5ms load / 5ms move (@ 5mm/ms) => 25mm
###   - 8ms load / 2ms move (@ 8mm/ms) => 16mm

$res = 1;
foreach ($times as $race => $maxtime) {
	$nb = 0;
	for ($load = 1; $load < $maxtime; $load ++) {
		// temps restant * vitesse
		if (($maxtime - $load) * $load > $dists[$race]) $nb++;
	}
	$res*= $nb;
}
printf("\nRes=%d\n\n", $res);
