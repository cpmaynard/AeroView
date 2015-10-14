# [AeroView] - A simple web based GUI for AeroSpike

## Getting Started

To use AeroView, you will need the following setup:
* PHP 5.3+
* Apache
* Aerospike PHP Client - https://github.com/aerospike/aerospike-client-php
* Aerospike Instance or Cluster

## Bugs and Issues
1) When connecting to far away hosts, there is timeout issues with reads. So far havent been able to fix this even with setting Aerospike::OPT_CONNECT_TIMEOUT, Aerospike::OPT_READ_TIMEOUT to super high values.

2) There is a column in sets for record count, the method being used is infoMany(sets/NAMESPACE) - there is a replication fator config, but no guarantee that the user will put all ip's in the config file for a cluster, which will give incorrect counts. Uncomment this feature only if you are listing all nodes in your aerospike cluster in the config.


