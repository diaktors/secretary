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
use Secretery\Controller\NoteController;
use Secretery\Service\Key as KeyService;
use Secretery\Service\Note as NoteService;
use Secretery\Service\Encryption as EncryptionService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;

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
                'module' => 'secretery',
                'controller' => 'Secretery\Controller\Index',
                'action' => 'index',
                'resource' => 'dashboard',
                'privilege' => 'use'
            ),
            'key' => array(
                'label' => 'Manage Key',
                'route' => 'secretery/default',
                'module' => 'secretery',
                'controller' => 'key',
                'action' => 'index',
                'resource' => 'key',
                'privilege' => 'use'
            ),
            'notes' => array(
                'label' => 'Manage Notes',
                'route' => 'secretery/note',
                'module' => 'secretery',
                'resource' => 'notes',
                'privilege' => 'use'
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
                    'note' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/note[/:action[/:id]]',
                            'constraints' => array(
                                'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'      => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'Note',
                                'action'        => 'index',
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
            'key-service' => function(ServiceManager $sm) {
                $service = new KeyService();
                /* @var $em EntityManager */
                $em = $sm->get('doctrine.entitymanager.orm_default');
                $service->setEntityManager($em);
                return $service;
            },
            'note-service' => function(ServiceManager $sm) {
                $service = new NoteService();
                /* @var $em EntityManager */
                $em         = $sm->get('doctrine.entitymanager.orm_default');
                /* @var $encService EncryptionService */
                $encService = $sm->get('encryption-service');
                $service->setEntityManager($em);
                $service->setEncryptionService($encService);
                return $service;
            },
        ),
        'invokables' => array(
            'encryption-service' => 'Secretery\Service\Encryption',
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
            'Secretery\Controller\Key' => function(ControllerManager $cm) {
                $controller = new KeyController();
                $controller->setKeyService($cm->getServiceLocator()->get('key-service'))
                    ->setEncryptionService($cm->getServiceLocator()->get('encryption-service'));
                return $controller;
            },
            'Secretery\Controller\Note' => function(ControllerManager $cm) {
                $controller = new NoteController();
                $controller->setNoteService($cm->getServiceLocator()->get('note-service'));
                return $controller;
            },
        )
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
            'secretery/note/index'    => __DIR__ . '/../view/secretery/note/index.phtml',
            'secretery/note/add'      => __DIR__ . '/../view/secretery/note/add.phtml',
            'zfc-user/user/login'     => __DIR__ . '/../view/zfc-user/login.phtml',
            'zfc-user/user/register'  => __DIR__ . '/../view/zfc-user/register.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
