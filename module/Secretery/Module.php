<?php
/**
 * Secretery Module
 *
 * @link      http://github.com/wesrc/Secretery
 * @copyright Wesrc (c) 2013 Wesrc UG (http://www.wesrc.com)
 * @license
 */

namespace Secretery;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use \Doctrine\Common\Persistence\PersistentObject;

class Module
{
    /**
     * @param  \Zend\Mvc\MvcEvent $e
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->setDoctrinePersistentObject($e);

        // Add translation for Validators
        /* @var $translator \Zend\I18n\Translator\Translator */
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            __DIR__ . '/../../vendor/zendframework/zendframework/resources/languages/de/Zend_Validate.php',
            'default',
            $translator->getLocale()
        );
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);

        // Attach to ZfcUser register.post event to create user role
        /* @var $zfcServiceEvents \Zend\EventManager\EventManager */
        $zfcServiceEvents = $e->getApplication()->getServiceManager()
            ->get('zfcuser_user_service')->getEventManager();
        $userService = $e->getApplication()->getServiceManager()->get('user-service');
        $zfcServiceEvents->attach('register.post', array($userService, 'saveUserRole'));
    }

    public function init(ModuleManager $moduleManager)
    {
        /*$sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            $controller = $e->getTarget();
            $route      = $controller->getEvent()->getRouteMatch();
            $controller->getEvent()->getViewModel()->setVariables(array(
                'controller' => $route->getParam('controller'),
                'action'     => $route->getParam('action'),
            ));
        }, 100);*/
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'navigation' => function (\Zend\View\HelperPluginManager $sm) {
                    /* @var $navigation \Zend\View\Helper\Navigation */
                    $navigation = $sm->get('Zend\View\Helper\Navigation');

                    $acl = $sm->getServiceLocator()
                        ->get('BjyAuthorize\Service\Authorize')
                        ->getAcl();

                    $role = $sm->getServiceLocator()
                        ->get('BjyAuthorize\Service\Authorize')
                        ->getIdentityProvider()
                        ->getIdentityRoles();

                    if (is_array($role) && isset($role[0])) {
                        $role = $role[0];
                    } else {
                        $role = 'guest';
                    }

                    $navigation->setAcl($acl)
                        ->setRole($role);

                    //\Zend\View\Helper\Navigation::setDefaultAcl($acl);
                    //\Zend\View\Helper\Navigation::setDefaultRole($role);

                    return $navigation;
                }
            )
        );
    }

    /**
     * @param  \Zend\Mvc\MvcEvent $e
     * @return void
     */
    protected function setDoctrinePersistentObject(MvcEvent $e)
    {
        $em = $e->getApplication()->getServiceManager()
            ->get('doctrine.entitymanager.orm_default');
        PersistentObject::setObjectManager($em);
    }

}
