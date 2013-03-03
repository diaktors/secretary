<?php
/**
 * Secretery Module config
 *
 * @link      http://github.com/wesrc/Secretery
 * @copyright Wesrc (c) 2013 Wesrc UG (http://www.wesrc.com)
 * @license
 */

namespace Secretery;

use Secretery\Controller\GroupController;
use Secretery\Controller\KeyController;
use Secretery\Controller\NoteController;
use Secretery\Controller\UserController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

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
            'groups' => array(
                'label' => 'Manage Groups',
                'route' => 'secretery/group',
                'module' => 'secretery',
                'resource' => 'groups',
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
            'user-settings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/user/settings',
                    'defaults' => array(
                        'controller' => 'Secretery\Controller\User',
                        'action'     => 'settings',
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
                    'group' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/group[/:action[/:id]]',
                            'constraints' => array(
                                'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'      => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'Group',
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
            'translator'     => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'Navigation'     => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'key-service'    => 'Secretery\Service\Factory\KeyFactory',
            'note-service'   => 'Secretery\Service\Factory\NoteFactory',
            'user-service'   => 'Secretery\Service\Factory\UserFactory',
            'group-service'  => 'Secretery\Service\Factory\GroupFactory',
            'logger-service' => 'Secretery\Service\Factory\LoggerFactory',
            'mail-service'   => 'Secretery\Service\Factory\MailFactory',
        ),
        'invokables' => array(
            'encryption-service' => 'Secretery\Service\Encryption',
            'groupMember-form'   => 'Secretery\Form\GroupMember',
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
                    ->setEncryptionService($cm->getServiceLocator()->get('encryption-service'))
                    ->setUserService($cm->getServiceLocator()->get('user-service'));
                return $controller;
            },
            'Secretery\Controller\Note' => function(ControllerManager $cm) {
                $controller = new NoteController();
                $controller->setNoteService($cm->getServiceLocator()->get('note-service'));
                $controller->setGroupService($cm->getServiceLocator()->get('group-service'));
                return $controller;
            },
            'Secretery\Controller\Group' => function(ControllerManager $cm) {
                $controller = new GroupController();
                $controller->setGroupService($cm->getServiceLocator()->get('group-service'))
                    ->setNoteService($cm->getServiceLocator()->get('note-service'))
                    ->setUserService($cm->getServiceLocator()->get('user-service'));
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
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
