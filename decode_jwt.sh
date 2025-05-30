#!/usr/bin/env bash
set -e

if [ -z "$1" ]; then
  echo "Usage: $0 <jwt>"
  exit 1
fi

TOKEN=$1

# 1) Extract the middle segment (the payload)
PAYLOAD_B64URL=$(printf '%s' "$TOKEN" | cut -d '.' -f2)

# 2) Convert Base64-URL to standard Base64, then decode
#    - Replace '-' with '+' and '_' with '/'
#    - Pad with '=' to multiple of 4 if necessary
P=$(printf '%s' "$PAYLOAD_B64URL" |
    tr '_-' '/+' |
    sed -E 's/=*$//')  # strip existing padding
PAD=$(printf '%s' "$P" | wc -c | awk '{ m = $1 % 4; print (m ? 4 - m : 0) }')
Padded=$(printf '%s' "$P"; printf '=%.0s' $(seq 1 $PAD))

DECODED=$(printf '%s' "$Padded" | base64 -d)

# 3) Pretty-print via PHP
php -r 'echo json_encode(json_decode($argv[1], true), JSON_PRETTY_PRINT), "\n";' "$DECODED"
