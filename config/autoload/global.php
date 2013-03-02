<?php

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'xyz',
                    'dbname'   => 'wesrc',
                    'password' => 'abc'
                )
            )
        ),
        'configuration' => array(
            'orm_default' => array(
                'metadata_cache'    => 'apc',
                'query_cache'       => 'apc',
                'result_cache'      => 'apc',
                'driver'            => 'orm_default',
                'generate_proxies'  => false,
                'proxy_dir'         => 'data/Secretery/Entity/Proxy',
                'proxy_namespace'   => 'Secretery\Entity\Proxy',
                'filters'           => array()
            )
        )
    ),

    'logger' => array(
        'writer'        => 'Stream',
        'writerOptions' => __DIR__ . '/../../data/log/app.log'
    ),
);
