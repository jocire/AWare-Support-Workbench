<?php
$path = dirname(__DIR__) . '/includes/class-aware-sw-admin.php';
$code = file_get_contents($path);
$must = ['>Troubleshooting</a>','aware-sw-plugin-select','disabled_plugins[]','Select all','Clear selection','Update Engineer Session','Stop Engineer Session / restore normal state'];
foreach ($must as $needle) { if (strpos($code,$needle)===false) { fwrite(STDERR,"Missing troubleshooting contract marker: {$needle}\n"); exit(1);} }
foreach (['Maintenance Scan','view=maintenance-scan','Pause scan','Scan selected targets'] as $forbidden) { if (strpos($code,$forbidden)!==false) { fwrite(STDERR,"Removed bulk-scan marker remains: {$forbidden}\n"); exit(1);} }
echo "troubleshooting isolation contract: PASS\n";
