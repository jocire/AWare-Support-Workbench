<?php
$root=__DIR__;
$groups=['behavior','contracts'];
$tests=[];
foreach($groups as $group){ foreach(glob($root.'/'.$group.'/*.php') ?: [] as $file) $tests[]=$file; }
foreach(glob($root.'/*.php') ?: [] as $file){ if(basename($file)==='run.php' || basename($file)==='test-lib.php') continue; $tests[]=$file; }
sort($tests);
$failed=0;
foreach($tests as $file){
    echo "==> ".str_replace($root.'/','',$file)."\n";
    $cmd=escapeshellarg(PHP_BINARY).' '.escapeshellarg($file);
    passthru($cmd,$code);
    if($code!==0){$failed++;}
}
echo "\n".count($tests)." test scripts, {$failed} failed.\n";
exit($failed?1:0);
