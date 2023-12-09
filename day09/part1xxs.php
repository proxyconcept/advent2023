<?php function x($s){$a=[$s];do{$q=[];$p=$d=$t=null;foreach($s as $c){
if($p!==null)$q[]=$d=$c-$p;if($d)$t=1;$p=$c;}$a[]=$s=$q;}while($t);
$n=0;foreach(array_reverse($a) as $q)$n=end($q)+$n;return $n;}
$s=0;foreach(file('input.txt') as $l)$s+=x(explode(' ',$l));echo($s);