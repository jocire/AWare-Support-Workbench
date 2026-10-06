#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
php tests/run.php
while IFS= read -r -d '' file; do php -l "$file" >/dev/null; done < <(find . -name '*.php' -print0)
echo "AWare Support Workbench test suite passed."
