<?php
return array(
    'modules' => array(
        'ZfcBase',
        'ZfcUser',
        'DoctrineModule',
        'DoctrineORMModule',
        'ZfcUserDoctrineORM',
        'BjyAuthorize',
        'ZfcAdmin',
        'ZfcTwitterBootstrap',
        'Secretary',
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
