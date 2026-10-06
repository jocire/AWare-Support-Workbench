<?php
require dirname(__DIR__) . '/test-lib.php';
$root=dirname(__DIR__,2);
foreach (['README.md','ROADMAP.md','wiki/Home.md','wiki/Installation.md','wiki/Troubleshooting.md','wiki/Architecture.md','wiki/Testing.md','wiki/Security-and-Privacy.md','wiki/Developer-Reference.md','wiki/Release-Checklist.md'] as $file) aware_test_assert(is_file($root.'/'.$file), "missing documentation file {$file}");
$readme=file_get_contents($root.'/README.md');
foreach (['Installation','Engineer Session isolation','Tests and release gates','Documentation'] as $n) aware_test_contains($n,$readme,'README coverage');
foreach (['Maintenance Scan','Targeted diagnostics','latest evidence','AWare_SW_Diagnostics'] as $n) aware_test_not_contains($n,$readme,'README removed-feature coverage');
$testing=file_get_contents($root.'/wiki/Testing.md');
foreach (['PHP/contracts','Browser UI','Full release gate','npm run test:php','npm run test:browser','npm test'] as $n) aware_test_contains($n,$testing,'testing manual coverage');
foreach (['Live isolation boundary','npm run test:isolation-live','aware-isolation-regression-fixture'] as $n) aware_test_not_contains($n,$testing,'removed live-isolation harness');
echo "PASS: README and GitHub Wiki documentation cover the final Engineer Session scope.\n";
