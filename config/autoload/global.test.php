<?php

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'host'     => '',
                    'port'     => '',
                    'user'     => '',
                    'dbname'   => '',
                    'password' => '',
                    'memory'   => true
                )
            )
        ),
        'configuration' => array(
            'orm_default' => array(
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',
                'driver'            => 'orm_default',
                'generate_proxies'  => true,
                'proxy_dir'         => 'data/Secretary/Entity/Proxy',
                'proxy_namespace'   => 'Secretary\Entity\Proxy',
                'filters'           => array()
            )
        )
    ),

    'translator' => array(
        'locale' => 'en_US',
    ),

    'logger' => array(
        'activate' => false,
    ),

    'mail' => array(
        'activate' => false,
    ),
);
