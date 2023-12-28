<?php
$inputs = file('input.txt', FILE_IGNORE_NEW_LINES);

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
	
	while ($todo) {
		$next = array();
		foreach ($todo as $action) {
			list($name, $pulse, $from) = $action;
			$mod = &$modules[$name];
			
			// Application de l'action (envoi d'un signal lo/hi) sur le module (empile les actions consécutives)
			if ($mod['type'] === '%') {         // soit un module flip-flop
				if ($pulse) continue;
				$pulse = $mod['status'] = ! $mod['status'];
			}
			else {                              // soit un module conjunction
				$mod['status'][$from] = $pulse;
#				$pulse = in_array(false, $mod['status']);
#				$pulse = false; foreach ($mod['status'] as $state) if (! $state) { $pulse = true; break; }
#				$pulse = ($pulse) ? in_array(false, $mod['status']) : true;
				if ($pulse) { $pulse = false; foreach ($mod['status'] as $state) if (! $state) { $pulse = true; break; } } else $pulse = true;
			}
			// aucun autre cas de module possible (broadcaster traité initialement)
			foreach ($mod['out'] as $out) {
				if ($out !== 'rx') $next[] = array($out, $pulse, $name);
				elseif (! $pulse) return false; // stop cycle (état final trouvé)
			}
		}
		$todo = $next;
	}
	return true;                                // next cycle (pile vide)
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
### 12.0s / 1000000
