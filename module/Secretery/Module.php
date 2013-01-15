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
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->setDoctrinePersistentObject($e);
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
                'navigation' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    /* @var $navigation \Zend\View\Helper\Navigation */
                    $navigation = $locator->get('Zend\View\Helper\Navigation');

                    // Setup ACL:
                    /*$acl = $sm->getServiceLocator()
                        ->get('BjyAuthorize\Service\Authorize')
                        ->getAcl();

                    $role = $sm->getServiceLocator()
                        ->get('BjyAuthorize\Service\Authorize')
                        ->getIdentityProvider()
                        ->getIdentityRoles();

                    if (is_array($role) && isset($role[0])) {
                        $role = $role[0];
                    }
                    // Store ACL and role in the proxy helper:
                    $navigation->setAcl($acl)
                        ->setRole($role);*/

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
