ui = true

listener "tcp" {
  address     = "0.0.0.0:8200"
  tls_disable = 1
}

storage "etcd" {
  address   = "http://etcd:2379"   # points at etcd service
  etcd_api  = "v3"
  path      = "vault/"             # top‐level key prefix in etcd
}