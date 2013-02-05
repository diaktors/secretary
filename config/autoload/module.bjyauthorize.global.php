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
        'identity_provider'     => 'BjyAuthorize\Provider\Identity\ZfcUserDoctrine',
        'unauthorized_strategy' => 'BjyAuthorize\View\UnauthorizedStrategy',
        'role_providers'        => array(
            'BjyAuthorize\Provider\Role\Doctrine' => array(),
        ),
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'pants' => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('guest', 'user'), 'dashboard', 'use'),
                    array(array('guest', 'user'), 'key', 'use')
                ),
                'deny' => array(),
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
        'template'  => 'error/403',
    ),
    'service_manager' => array(
        'factories' => array(
            'BjyAuthorize\Guard\Controller' => function (\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                $config = $sl->get('config');
                return new \BjyAuthorize\Guard\Controller($config['bjyauthorize']['guards']['BjyAuthorize\Guard\Controller'], $sl);
            },
            'BjyAuthorize\Guard\Route' => function (\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                $config = $sl->get('config');
                return new \BjyAuthorize\Guard\Route($config['bjyauthorize']['guards']['BjyAuthorize\Guard\Route'], $sl);
            },
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'error/403' => __DIR__ . '/../../vendor/bjyoungblood/bjy-authorize/view/error/403.phtml',
        ),
    ),
);
