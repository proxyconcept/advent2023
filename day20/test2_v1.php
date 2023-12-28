<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

// Création de la liste des modules (avec leur nom, type et variable d'état(s))
$modules = array();
foreach ($inputs as $line) {
	if (! preg_match('/^([%&]?)([a-z]+) -> ([a-z, ]+)$/', $line, $m)) die("Invalid module : $line\n");
	list(, $type, $name, $outputs) = $m;
	$modules[$name] = array(
		'type'   => ($type === '' && $name === 'broadcaster') ? '{' : $type,
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
	$todo = array();
	if (isset($modules[$name])) switch ($modules[$name]['type']) {
		case '%' :  // soit un module flip-flop
			if (! $pulse) {
				$modules[$name]['status'] = ! $modules[$name]['status'];
				foreach ($modules[$name]['out'] as $out) $todo[] = array($out, $modules[$name]['status'], $name);
			}
			break;
		case '&' :  // soit un module conjunction
			$modules[$name]['status'][$from] = $pulse;
			if ($pulse) foreach ($modules[$name]['status'] as $state) if (! $state) { $pulse = false; break; }
			foreach ($modules[$name]['out'] as $out) $todo[] = array($out, ! $pulse, $name);
			break;
		case '{' :  // soit le module broadcaster
			foreach ($modules[$name]['out'] as $out) $todo[] = array($out, $pulse, $name);
			break;
	}
	return $todo;
}

// Déclenchement d'un cycle (appui sur le bouton et enchainement des actions successives)
function run_button() {
	$todo = array(['broadcaster', false, 'button']);
	
#	while (count($todo)) {
#	while (! empty($todo)) {
#	while ($todo !== []) {
#	while (true) {
	while ($todo) {
		$next = array();
		foreach ($todo as $action) {
			if ($action[1] === false && $action[0] === 'rx') return false; // stop cycle (état final trouvé)
			
			foreach (run_module($action[0], $action[1], $action[2]) as $a) $next[] = $a;
		}
#		if (! $next) return true;                                          // next cycle (pile vide)
		$todo = $next;
	}
	return true;                                                           // next cycle (pile vide)
}

// Lancement des cycles... (appuis sur le bouton)
$timer = microtime(true);
for ($cycle = 1; run_button() && ($cycle < 1000000); $cycle++);
printf("\nCycle=%d (%0.3fs) : %s\n\n", $cycle, microtime(true) - $timer, basename(__FILE__));

$res_states = array(
	'100000'  => '{"dt":false,"tl":false,"vx":true,"sz":false,"kj":false,"pm":true,"fc":true,"tb":{"pm":true,"gf":false,"jm":false,"qn":false,"gb":false,"xh":true,"db":false,"cd":true},"df":false,"qq":false,"sl":false,"broadcaster":false,"pv":{"tb":true},"gf":false,"pb":false,"gr":false,"gs":false,"tx":true,"jm":false,"bh":false,"rt":false,"qh":{"fl":true},"lb":true,"pd":true,"qn":false,"gb":false,"xm":{"kc":true},"mv":true,"gz":true,"js":true,"hp":false,"nk":true,"kh":{"pv":false,"qh":false,"xm":false,"hz":false},"zc":true,"mp":true,"zm":false,"xh":true,"db":false,"sx":false,"hz":{"hd":true},"vj":true,"zq":true,"lj":true,"lg":false,"fl":{"bh":false,"lb":true,"gz":true,"mb":true,"vz":false,"bb":false,"xv":false},"kc":{"vx":true,"sz":false,"gs":false,"rt":false,"nk":true,"lj":true,"lg":false,"lq":true,"zb":true},"lq":true,"hd":{"dt":false,"tl":false,"kj":false,"pb":false,"gr":false,"mv":true,"hp":false,"mp":true,"zq":true,"fm":false,"jk":false},"mb":true,"vz":false,"fm":false,"bb":false,"zb":true,"xp":true,"jk":false,"xv":false,"sk":false,"cd":true}',
	'1000000' => '{"dt":false,"tl":true,"vx":true,"sz":false,"kj":false,"pm":true,"fc":true,"tb":{"pm":true,"gf":true,"jm":true,"qn":false,"gb":false,"xh":true,"db":false,"cd":true},"df":false,"qq":true,"sl":true,"broadcaster":false,"pv":{"tb":true},"gf":true,"pb":true,"gr":false,"gs":true,"tx":true,"jm":true,"bh":true,"rt":true,"qh":{"fl":true},"lb":false,"pd":true,"qn":false,"gb":false,"xm":{"kc":true},"mv":false,"gz":true,"js":false,"hp":true,"nk":false,"kh":{"pv":false,"qh":false,"xm":false,"hz":false},"zc":true,"mp":false,"zm":false,"xh":true,"db":false,"sx":false,"hz":{"hd":true},"vj":true,"zq":false,"lj":true,"lg":true,"fl":{"bh":true,"lb":false,"gz":true,"mb":false,"vz":false,"bb":false,"xv":true},"kc":{"vx":true,"sz":false,"gs":true,"rt":true,"nk":false,"lj":true,"lg":true,"lq":false,"zb":false},"lq":false,"hd":{"dt":false,"tl":true,"kj":false,"pb":true,"gr":false,"mv":false,"hp":true,"mp":false,"zq":false,"fm":true,"jk":false},"mb":false,"vz":false,"fm":true,"bb":false,"zb":false,"xp":true,"jk":false,"xv":true,"sk":true,"cd":true}',
);
if ($res_states[$cycle] !== json_encode(array_map(function($mod){ return $mod['status']; }, $modules))) die("WRONG STATES !!!\n");
### 17.8s / 1000000
