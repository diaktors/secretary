<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * PHP Version 5
 *
 * @category Module
 * @package  Secretary
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary;

use Secretary\Controller;
use Secretary\Service;
use SecretaryCrypt\Crypt;
use Zend\Mvc\Controller\ControllerManager;

/**
 * Module Config
 */
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
                'module' => 'secretary',
                'controller' => 'Secretary\Controller\Index',
                'action' => 'index',
                'resource' => 'dashboard',
                'privilege' => 'use'
            ),
            'key' => array(
                'label' => 'Manage Key',
                'route' => 'secretary/default',
                'module' => 'secretary',
                'controller' => 'key',
                'action' => 'index',
                'resource' => 'key',
                'privilege' => 'use'
            ),
            'notes' => array(
                'label' => 'Manage Notes',
                'route' => 'secretary/note',
                'module' => 'secretary',
                'resource' => 'notes',
                'privilege' => 'use'
            ),
            'groups' => array(
                'label' => 'Manage Groups',
                'route' => 'secretary/group',
                'module' => 'secretary',
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
                        'controller' => 'Secretary\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'user-settings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/user/settings',
                    'defaults' => array(
                        'controller' => 'Secretary\Controller\User',
                        'action'     => 'settings',
                    ),
                ),
            ),
            'secretary' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/secretary',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Secretary\Controller',
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
            'Navigation'     => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'key-service'    => 'Secretary\Service\Factory\KeyFactory',
            'note-service'   => 'Secretary\Service\Factory\NoteFactory',
            'user-service'   => 'Secretary\Service\Factory\UserFactory',
            'group-service'  => 'Secretary\Service\Factory\GroupFactory',
            'logger-service' => 'Secretary\Service\Factory\LoggerFactory',
            'mail-service'   => 'Secretary\Service\Factory\MailFactory',
        ),
        'invokables' => array(
            'crypt-service' => 'SecretaryCrypt\Crypt',
            'groupMember-form' => 'Secretary\Form\GroupMember',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),

    // Translator
    'translator' => array(
        'locale' => 'pt_BR',
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
            //'Secretary\Controller\Index' => 'Secretary\Controller\IndexController',
        ),
        'factories' => array(
            'Secretary\Controller\Index'  => function(ControllerManager $cm) {
                $controller = new Controller\IndexController();
                /** @var Service\Note $noteService */
                $noteService = $cm->getServiceLocator()->get('note-service');
                $controller->setNoteService($noteService);
                return $controller;
            },
            'Secretary\Controller\Key' => function(ControllerManager $cm) {
                $controller = new Controller\KeyController();
                /** @var Service\Key $keyService */
                $keyService = $cm->getServiceLocator()->get('key-service');
                /** @var Service\User $userService */
                $userService = $cm->getServiceLocator()->get('user-service');
                /** @var Crypt $cryptService */
                $cryptService = $cm->getServiceLocator()->get('crypt-service');
                $controller->setKeyService($keyService)
                    ->setCryptService($cryptService)
                    ->setUserService($userService);
                return $controller;
            },
            'Secretary\Controller\Note' => function(ControllerManager $cm) {
                $controller = new Controller\NoteController();
                /** @var Service\Note $noteService */
                $noteService = $cm->getServiceLocator()->get('note-service');
                /** @var Service\Group $groupService */
                $groupService = $cm->getServiceLocator()->get('group-service');
                $controller->setNoteService($noteService);
                $controller->setGroupService($groupService);
                return $controller;
            },
            'Secretary\Controller\Group' => function(ControllerManager $cm) {
                $controller = new Controller\GroupController();
                /** @var Service\Group $groupService */
                $groupService = $cm->getServiceLocator()->get('group-service');
                /** @var Service\Note $noteService */
                $noteService = $cm->getServiceLocator()->get('note-service');
                /** @var Service\User $userService */
                $userService = $cm->getServiceLocator()->get('user-service');
                $controller->setGroupService($groupService)
                    ->setNoteService($noteService)
                    ->setUserService($userService);
                return $controller;
            },
            'Secretary\Controller\User' => function(ControllerManager $cm) {
                $controller = new Controller\UserController();
                /** @var Service\User $userService */
                $userService = $cm->getServiceLocator()->get('user-service');
                $controller->setUserService($userService);
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
            'secretary/index/index'   => __DIR__ . '/../view/secretary/index/index.phtml',
            'secretary/key/index'     => __DIR__ . '/../view/secretary/key/index.phtml',
            'secretary/key/success'   => __DIR__ . '/../view/secretary/key/success.phtml',
            'secretary/note/index'    => __DIR__ . '/../view/secretary/note/index.phtml',
            'secretary/note/add'      => __DIR__ . '/../view/secretary/note/add.phtml',
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
