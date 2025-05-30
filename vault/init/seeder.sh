#!/usr/bin/env bash
set -e

export VAULT_ADDR='http://127.0.0.1:8200'
export VAULT_TOKEN='root'

# wait for Vault to be ready
until vault status >/dev/null 2>&1; do sleep 1; done

vault kv put secret/data/oauth \
  public=@/vault/secrets/oauth-public.key \
  private=@/vault/secrets/oauth-private.key
