
#!/bin/sh

set -e

echo 'vault init...'

export VAULT_ADDR=http://vault:8200
#export VAULT_TOKEN='root'

# give some time for Vault to start and be ready
sleep 5

# login with root token at $VAULT_ADDR
vault login root

# enable the KV secrets engine at the default path (demo/)
vault secrets enable -path=data -version=2 kv

# write a secret to the kv store
vault kv put secret/data/apisix/oauth \
  public="oauth-public.key" \
  private="oauth-private.key"

# read the secret back
#vault kv get data/oauth/oauth-public-key

# create policy to read secret
vault policy write app-policy app-policy.hcl

echo 'vault end...'
