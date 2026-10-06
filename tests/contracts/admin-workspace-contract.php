<?php
require dirname(__DIR__) . '/test-lib.php';
$src=file_get_contents(dirname(__DIR__,2).'/includes/class-aware-sw-admin.php');
foreach (["Troubleshooting", "disabled_plugins[]", "Select all", "Clear selection", "Update Engineer Session", "Stop Engineer Session / restore normal state", "ENGINEER SESSION"] as $n) aware_test_contains($n,$src,'focused admin workspace contract');
foreach (["Maintenance Scan", "view=maintenance-scan", "Pause scan", "Scan selected targets"] as $n) aware_test_not_contains($n,$src,'bulk maintenance workflow must stay removed');
foreach (["Targeted diagnostics", "aware_sw_open_target", "aware-sw-target-url", "aware-sw-evidence"] as $n) aware_test_not_contains($n,$src,'removed diagnostics UI');
echo "PASS: focused troubleshooting admin UX contract.\n";
