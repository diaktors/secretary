<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\\DBAL\\Driver\\PDOMySql\\Driver',
                'params' => array(
                    'host' => 'localhost',
                    'port' => '3306',
                    'user' => 'xyz',
                    'dbname' => 'wesrc',
                    'password' => 'abc',
                ),
            ),
        ),
        'configuration' => array(
            'orm_default' => array(
                'metadata_cache' => 'apc',
                'query_cache' => 'apc',
                'result_cache' => 'apc',
                'driver' => 'orm_default',
                'generate_proxies' => false,
                'proxy_dir' => 'data/Secretary/Entity/Proxy',
                'proxy_namespace' => 'Secretary\\Entity\\Proxy',
                'filters' => array(),
            ),
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
    ),
    'logger' => array(
        'activate' => false,
        'writer' => 'Stream',
        'writerOptions' => '/Users/mischosch/Sites/wesrc/secretary/config/autoload/../../data/log/app.log',
    ),
    'mail' => array(
        'activate' => false,
        'default_email' => 'xyz@abc.com',
        'default_from' => 'noreply@abc.com',
        'domain_url' => 'http://abc.com/',
        'transport' => array(
            'type' => 'smtp',
            'options' => array(
                'name' => 'my localhost',
                'host' => 'localhost',
                'connection_class' => 'login',
                'connection_config' => array(
                    'username' => 'xyz@abc.com',
                    'password' => '123',
                ),
            ),
        ),
    ),
    'zf-mvc-auth' => array(
        'authentication' => array(
            'http' => array(
                'accept_schemes' => array(
                    0 => 'basic',
                ),
                'realm' => 'SecretaryAPI',
            ),
        ),
    ),
);
