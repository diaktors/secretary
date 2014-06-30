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
        'identity_provider'     => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',
        'unauthorized_strategy' => 'BjyAuthorize\View\UnauthorizedStrategy',
        'role_providers'        => array(
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                'object_manager'    => 'doctrine.entitymanager.orm_default',
                'role_entity_class' => 'Secretary\Entity\Role'
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
                array('route' => 'user-settings', 'roles' => array('user')),
                array('route' => 'secretary/default', 'roles' => array('user')),
                array('route' => 'secretary/note', 'roles' => array('keyuser')),
                array('route' => 'secretary/group', 'roles' => array('keyuser')),

                array('route' => 'doctrine_orm_module_yuml', 'roles' => array('user')),

                array('route' => 'zf-apigility', 'roles' => array('user')),
                array('route' => 'zf-apigility/documentation', 'roles' => array('user')),
                array('route' => 'zf-apigility/ui', 'roles' => array('user')),
                array('route' => 'zf-apigility/api', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/dashboard', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/settings-dashboard', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/cache-enabled', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/fs-permissions', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/config', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/config/module', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/source', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/filters', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/validators', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/hydrators', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/module-enable', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/versioning', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/default-version', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/module', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/module/authorization', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/module/rest-service', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/module/rpc-service', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/module/rest-service/input-filter', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/authentication', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/authentication/oauth2', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/authentication/http-basic', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/authentication/http-digest', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/db-adapter', 'roles' => array('user')),
                array('route' => 'zf-apigility/api/content-negotiation', 'roles' => array('user')),
                array('route' => 'zf-apigility/oauth', 'roles' => array('user')),
                array('route' => 'zf-apigility/oauth/authorize', 'roles' => array('user')),
                array('route' => 'zf-apigility/oauth/resource', 'roles' => array('user')),
                array('route' => 'zf-apigility/oauth/code', 'roles' => array('user')),

                array('route' => 'secretaryapi.rest.doctrine.group', 'roles' => array('guest', 'user')),
                array('route' => 'secretaryapi.rest.doctrine.user', 'roles' => array('guest', 'user')),
                array('route' => 'secretaryapi.rest.doctrine.note', 'roles' => array('guest', 'user')),
                array('route' => 'secretaryapi.rest.doctrine.user2-note', 'roles' => array('guest', 'user')),
                array('route' => 'secretaryapi.rest.doctrine.key', 'roles' => array('guest', 'user')),
            ),
        ),
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'dashboard' => array(),
                'key'       => array(),
                'notes'     => array(),
                'groups'    => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('user'), 'dashboard', 'use'),
                    array(array('user'), 'key', 'use'),
                    array(array('keyuser'), 'notes', 'use'),
                    array(array('keyuser'), 'groups', 'use')
                ),
                'deny' => array(),
            ),
        ),
        'template'  => 'error/403',
    ),
    'view_manager' => array(
        'template_map' => array(
            'error/403' => __DIR__ . '/../../module/Secretary/view/error/403.phtml',
        ),
    ),
);
