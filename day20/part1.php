<?php
$inputs = file('input0.txt', FILE_IGNORE_NEW_LINES);

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
		'type'   => ($type === '' && $name === 'broadcaster') ? '*' : $type,
		'status' => ($type === '&') ? array() : false,
		'out'    => explode(', ', $outputs),
	);
}

// Initialisation de la variable d'état des conjunctions (liste de ces modules en entrée)
foreach ($modules as $name => $mod) foreach ($mod['out'] as $out) {
	if (! isset($modules[$out])) continue;
	if ($modules[$out]['type'] === '&') $modules[$out]['status'][$name] = false;
}

// Application d'une action (envoi d'un signal lo/hi) sur un module (retourne les actions consécutives)
function run_module($name, $pulse, $from) {
	global $modules;
	printf("  > Send pulse : %12s --[%s]--> %s\n", $from, $pulse?"hi":"lo", $name);
	$todo = array();
	if (isset($modules[$name])) switch ($modules[$name]['type']) {
		case '%' :
			if (! $pulse) {
				$modules[$name]['status'] = ! $modules[$name]['status'];
				foreach ($modules[$name]['out'] as $out) $todo[] = array($out, $modules[$name]['status'], $name);
			}
			break;
		case '&' :
			$modules[$name]['status'][$from] = $pulse;
			if ($pulse) foreach ($modules[$name]['status'] as $state) if (! $state) { $pulse = false; break; }
			foreach ($modules[$name]['out'] as $out) $todo[] = array($out, ! $pulse, $name);
			break;
		case '*' :
			foreach ($modules[$name]['out'] as $out) $todo[] = array($out, $pulse, $name);
			break;
	}
	return $todo;
}

// Déclenchement d'un cycle (appui sur le bouton et enchainement des actions successives)
function run_button() {
	global $counters;
	$step = 0;
	$todo = array(['broadcaster', false, 'button']);
	
	while (count($todo)) {
		printf("=== Run step %d :\n", ++$step);
		$next = array();
		foreach ($todo as $action) {
			$next = array_merge($next, run_module($action[0], $action[1], $action[2]));
			$counters[ ($action[1]) ? 'hi' : 'lo' ]++;
		}
		$todo = $next;
	}
}

// Lancement de 1000 cycles (= 1000 appuis sur le bouton) avec compteurs des signaux
$cycle = 0;
$counters = array('lo' => 0, 'hi' => 0);
$ini_states = json_encode(array_map(function($mod){ return $mod['status']; }, $modules));
printf("[%04d] Modules states : %s\n\n", $cycle, $ini_states);

while ($cycle++ < 1000) {
	run_button();
	$cur_states = json_encode(array_map(function($mod){ return $mod['status']; }, $modules));
	printf("[%04d] Modules states : %s\n\n", $cycle, $cur_states);
}
printf("Counters lo=%d / hi=%d : Res=%d\n\n", $counters['lo'], $counters['hi'], $counters['lo'] * $counters['hi']);
