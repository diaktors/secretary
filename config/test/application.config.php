<?php
return array(
    'modules' => array(
        'Secretary',
        'DoctrineModule',
        'DoctrineORMModule',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineORM',
        'BjyAuthorize',
        'ZfcAdmin',
        'ZfcTwitterBootstrap',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/autoload/{,*.}{global,test}.php',
        ),
        'module_paths' => array(
            '../../../module',
            '../../../vendor',
        ),
    ),
);
