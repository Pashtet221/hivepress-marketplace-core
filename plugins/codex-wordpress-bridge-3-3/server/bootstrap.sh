#!/usr/bin/env bash
set -euo pipefail
RUNTIME="$1"
mkdir -p "$RUNTIME"
export PLAYWRIGHT_BROWSERS_PATH="$RUNTIME/browsers"
if [[ ! -f "$RUNTIME/package.json" ]]; then
  cat > "$RUNTIME/package.json" <<'JSON'
{"private":true,"dependencies":{"playwright":"^1.54.1","sharp":"^0.34.3"}}
JSON
fi
if [[ ! -d "$RUNTIME/node_modules/playwright" || ! -d "$RUNTIME/node_modules/sharp" ]]; then
  npm install --prefix "$RUNTIME" --omit=dev --no-audit --no-fund
fi
if [[ ! -d "$PLAYWRIGHT_BROWSERS_PATH" ]] || ! find "$PLAYWRIGHT_BROWSERS_PATH" -maxdepth 1 -type d -name 'chromium-*' | grep -q .; then
  (cd "$RUNTIME" && npx playwright install chromium)
fi
