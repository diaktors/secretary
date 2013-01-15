<?php
/**
 * Secretery Module config
 *
 * @link      http://github.com/wesrc/Secretery
 * @copyright Wesrc (c) 2013 Wesrc UG (http://www.wesrc.com)
 * @license
 */

namespace Secretery;

use Secretery\Controller\KeyController;
use Secretery\Mapper\Key as KeyMapper;

return array(

    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            ),
        ),
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array('Gedmo\Timestampable\TimestampableListener', 'Gedmo\Sluggable\SluggableListener')
            )
        ),
    ),

    // Navigation
    'navigation' => array(
        'default' => array(
            'dashboard' => array(
                'label' => 'Dashboard',
                'route' => 'home',
                'module' => 'wesrc',
                'controller' => 'Secretery\Controller\Index',
                'action' => 'index',
                'resource' => 'route/secretery/dashboard'
            ),
            'key' => array(
                'label' => 'Manage Key',
                'route' => 'secretery/default',
                'module' => 'wesrc',
                'controller' => 'key',
                'action' => 'index',
                'resource' => 'route/secretery/key'
            ),
        ),
    ),

    // Router config
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Secretery\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'secretery' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/secretery',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Secretery\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    // Service Manager
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'key-mapper' => function($sm) {
                $service = new KeyMapper();
                $em = $sm->get('doctrine.entitymanager.orm_default');
                $service->setEntityManager($em);
                return $service;
            },
        ),
        'invokables' => array(
            'key-service' => 'Secretery\Service\Key',
        ),
    ),

    // Translator
    'translator' => array(
        'locale' => 'de_DE',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),

    // Controllers
    'controllers' => array(
        'invokables' => array(
            'Secretery\Controller\Index' => 'Secretery\Controller\IndexController',
        ),
        'factories' => array(
            'Secretery\Controller\Key'   => function($sm) {
                $controller = new KeyController();
                $keyService = $sm->getServiceLocator()->get('key-service');
                $keyMapper  = $sm->getServiceLocator()->get('key-mapper');
                $controller->setKeyMapper($keyMapper)
                    ->setKeyService($keyService);
                return $controller;
            },
        )
    ),

    // Controller PlugIns
    'controller_plugins' => array(
        'invokables' => array(
            //'zfcuserauthentication' => 'ZfcUser\Controller\Plugin\ZfcUserAuthentication',
        ),
    ),

    // View
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'secretery/index/index'   => __DIR__ . '/../view/secretery/index/index.phtml',
            'secretery/key/index'     => __DIR__ . '/../view/secretery/key/index.phtml',
            'secretery/key/success'   => __DIR__ . '/../view/secretery/key/success.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
