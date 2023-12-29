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
foreach ($modules as $name => $mod) foreach ($mod['out'] as $out) {
	if (! isset($modules[$out])) continue;
	if ($modules[$out]['type'] === '&') $modules[$out]['status'][$name] = false;
}

// Initialisation des actions avec celles du broadcaster (son cas ne sera plus à traiter ensuite)
$starter = array();
foreach ($modules['broadcaster']['out'] as $out) $starter[] = array($out, false, 'broadcaster');

// Déclenchement d'un cycle (appui sur le bouton et enchainement des actions successives)
function run_button() {
	global $modules, $starter;
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
				if ($out !== 'rx') $next[] = array($out, $pulse, $action[0]);
				elseif (! $pulse) return false; // stop cycle (état final trouvé)
			}
		}
		if (! $next) return true;               // next cycle (pile vide)
		$todo = $next;
	}
}

// Lancement des cycles... (appuis sur le bouton)
$cycle = 1; while (run_button()) $cycle++;
printf("\nCycle=%d\n\n", $cycle);
