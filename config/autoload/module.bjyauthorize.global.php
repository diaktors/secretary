<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'bjyauthorize' => array(
        'default_role'          => 'guest',
        'identity_provider'     => 'BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity',
        'unauthorized_strategy' => 'BjyAuthorize\View\UnauthorizedStrategy',
        'role_providers'        => array(
            'BjyAuthorize\Provider\Role\DoctrineEntity' => array(
                'role_entity_class' => 'Secretery\Entity\Role'
            ),
        ),
        'guards' => array(
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'zfcuser', 'roles' => array('user')),
                array('route' => 'zfcuser/logout', 'roles' => array('user')),
                array('route' => 'zfcuser/changepassword', 'roles' => array('user')),
                array('route' => 'zfcuser/changeemail', 'roles' => array('user')),
                array('route' => 'zfcuser/login', 'roles' => array('guest')),
                array('route' => 'zfcuser/register', 'roles' => array('guest')),
                array('route' => 'home', 'roles' => array('guest', 'user')),
                array('route' => 'secretery/default', 'roles' => array('user')),
                array('route' => 'secretery/note', 'roles' => array('user')),
            ),
        ),
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'dashboard' => array(),
                'key' => array(),
                'notes' => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('user'), 'dashboard', 'use'),
                    array(array('user'), 'key', 'use'),
                    array(array('user'), 'notes', 'use')
                ),
                'deny' => array(),
            ),
        ),
        'template'  => 'error/403',
    ),
    'view_manager' => array(
        'template_map' => array(
            'error/403' => __DIR__ . '/../../module/Secretery/view/error/403.phtml',
        ),
    ),
);
