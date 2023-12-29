<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

/**
 * Modules types:
 * - flip-flop   (%) : input hi ignored / lo switch ; output lo (off) / hi (on)   ; default switch state off
 * - conjunction (&) : input update memory hi / lo  ; output hi / lo (all mem hi) ; default inputs memory lo
 * - broadcaster [*] : input hi / lo                ; output hi / lo (same as input)
 */

// Création de la liste des modules (avec leur nom, type et variable d'état(s))
$modules = array();
foreach ($inputs as $line) {
	if (! preg_match('/^([%&]?)([a-z]+) -> ([a-z, ]+)$/', $line, $m)) die("Invalid module : $line\n");
	list(, $type, $name, $outputs) = $m;
	$modules[$name] = array(
		'type'   => $type,
		'status' => ($type === '&') ? array() : false,
		'out'    => explode(', ', $outputs),
	);
}

// Initialisation de la variable d'état des conjunctions (liste de ces modules en entrée)
$mod_last = null;
foreach ($modules as $name => $mod) foreach ($mod['out'] as $out) {
	if (array_key_exists($out, $modules)) {
		if ($modules[$out]['type'] === '&') $modules[$out]['status'][$name] = false;
	} else $mod_last = $out;
}
printf(">>> Module last : '%s'\n", $mod_last);

// Recherche du dernier module conjunction en charge d'alimenter le module final 'rx'
$mod_prev = null;
foreach ($modules as $name => $mod) foreach ($mod['out'] as $out) {
	if ($out == 'rx') { $mod_prev = $name; break 2; }
}
printf(">>> Module prev : '%s'\n", $mod_prev);

// Initialisation des actions avec celles du broadcaster (son cas ne sera plus à traiter ensuite)
$starter = array();
foreach ($modules['broadcaster']['out'] as $out) $starter[] = array($out, false, 'broadcaster');

// Déclenchement d'un cycle (appui sur le bouton et enchainement des actions successives)
function run_button($cycle) {
	global $modules, $starter, $mod_prev, $loops;
	$todo = $starter;
	
	while (true) {
		$next = array();
		foreach ($todo as $action) {
			$mod = &$modules[$action[0]];
			
			// Application de l'action (envoi d'un signal lo/hi) sur le module (empile les actions consécutives)
			if ($mod['type'] === '%') {         // soit un module flip-flop
				if ($action[1]) continue;
				$pulse = $mod['status'] = ! $mod['status'];
			}
			elseif ($mod['type'] === '&') {     // soit un module conjunction
				$mod['status'][$action[2]] = $action[1];
				if ($action[1]) { $pulse = false; foreach ($mod['status'] as $state) if (! $state) { $pulse = true; break; } } else $pulse = true;
			}
			// aucun autre cas de module possible (broadcaster traité initialement)
			foreach ($mod['out'] as $out) {
				if ($out != 'rx') $next[] = array($out, $pulse, $action[0]);
				
				// envoi d'un signal 'hi' vers une entrée du dernier module conjunction ?
				if (($out === $mod_prev) && $pulse) $loops[$action[0]][] = $cycle;
			}
		}
		if (! $next) break;                     // fin du cycle (pile vide)
		$todo = $next;
	}
	
	// nombre de boucles détectées suffisant pour arrêter les cycles (5 minimum pour chaque entrées) ?
	foreach ($loops as $cycles) if (count($cycles) < 5) return true;
	return false;
}

// Lancement des cycles... (appuis sur le bouton)
$loops = array_fill_keys(array_keys($modules[$mod_prev]['status']), []);
$cycle = 1; while (run_button($cycle)) $cycle++;
printf("\nCycle=%d\n\n", $cycle);

// Vérification des boucles trouvées pour chacune des entrées du module conjunction final
$found = array();
foreach ($loops as $mod => $cycles) {
	$found[] = $loop = reset($cycles);
	printf("  > Loops for '%s' (%d cycles) : %s\n", $mod, $loop, implode(", ", $cycles));
	foreach ($cycles as $i => $cycle) if ($cycle !== $loop * ($i+1)) die("Invalid loop!\n");
}

// Recherche du 1er nombre de cycles divisible par chaque longueur de boucles (PPCM)
function pgcd_euclide($a, $b = 0) {
	while ($r = $b) { $b = $a % $b; $a = $r; }
	return $a;
}
$pgcd = 0; foreach ($found as $f) $pgcd = pgcd_euclide($f, $pgcd);
$ppcm = 1; foreach ($found as $f) $ppcm*= $f; $ppcm /= $pgcd;
printf("\nCycles=%d (PPCM)\n\n", $ppcm);
