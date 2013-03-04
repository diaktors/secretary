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
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Doctrine\Common\Persistence\PersistentObject;
use Secretary\View\Helper\Markdown;

/**
 * Secretary Module
 *
 * @category Module
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class Module implements BootstrapListenerInterface,
    FormElementProviderInterface,
    InitProviderInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ViewHelperProviderInterface
{
    /**
     * @param  \Zend\Mvc\MvcEvent $e
     * @return void
     */
    public function onBootstrap(EventInterface $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Set Doctrine PersistentObject
        $this->setDoctrinePersistentObject($e);

        // Add translation for Validators
        $this->initTranslation($e);

        // Attach to ZfcUser register.post event to create user role, logging/email
        $this->initUserRegistrationHook($e);

        // Attach Logging Events
        $this->initLogger($e);

        // Attach Mail Events
        $this->initMail($e);

        // Set User Locale
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'selectUserLocale'));

        return;
    }

    /**
     * @param  \Zend\ModuleManager\ModuleManagerInterface $moduleManager
     * @return void
     */
    public function init(ModuleManagerInterface $moduleManager)
    {

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
                },
                'SecretaryMarkdown' => function($sm) {
                    $locator = $sm->getServiceLocator();
                    return new Markdown($locator->get('Request'));
                },
            )
        );
    }

    /**
     * @return array
     */
    public function getFormElementConfig()
    {
        return array(
            'factories' => array('Secretary\Form\GroupMember' => function($sm) {
                $serviceLocator = $sm->getServiceLocator();
                $em = $serviceLocator->get('doctrine.entitymanager.orm_default');
                $form = new \Secretary\Form\GroupMember();
                $form->setObjectManager($em);
                return $form;
            })
        );
    }

    /**
     * Select the admin layout based on route name
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function selectUserLocale(MvcEvent $e)
    {
        $app         = $e->getParam('application');
        $sm          = $app->getServiceManager();
        $zfcUserAuth = $sm->get('controllerPluginManager')->get('zfcUserAuthentication');

        if (!$zfcUserAuth->hasIdentity()) {
            return;
        }

        $identity = $zfcUserAuth->getIdentity();
        $identity->getLanguage();

        $sm->get('translator')->setLocale($identity->getLanguage());
        return;
    }

    /**
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    protected function initLogger(EventInterface $e)
    {
        /* @var \Secretary\Service\Logger $loggerService */
        $loggerService = $e->getApplication()->getServiceManager()->get('logger-service');
        /* @var \Zend\EventManager\SharedEventManager $sharedEvents */
        $sharedEvents  = $e->getApplication()->getEventManager()->getSharedManager();
        $identifier    = array(
            'Zend\Mvc\Controller\AbstractActionController',
            'Secretary\Service\Base'
        );
        $sharedEvents->attach($identifier, 'logError', function ($e) use ($loggerService) {
            $message = $e->getParam('message', 'No message provided');
            $target  = $e->getTarget();
            $message = sprintf('%s - %s', $target, $message);
            $loggerService->logError($message);
        });
        $sharedEvents->attach($identifier, 'logViolation', function ($e) use ($loggerService) {
            $message = $e->getParam('message', 'No message provided');
            $target  = $e->getTarget();
            $message = sprintf('%s - %s', $target, $message);
            $loggerService->logViolation($message);
        });
        $sharedEvents->attach($identifier, 'logInfo', function ($e) use ($loggerService) {
            $message = $e->getParam('message', 'No message provided');
            $target  = $e->getTarget();
            $message = sprintf('%s - %s', $target, $message);
            $loggerService->logInfo($message);
        });
        $sharedEvents->attach('Zend\Mvc\Application', 'dispatch.error', function ($e) use ($loggerService) {
            $exception = $e->getParam('exception');
            if (empty($exception)) {
                return;
            }
            $target  = get_class($e->getTarget()) . '::l' . $exception->getLine();
            $message = sprintf('%s - %s', $target, $exception->getMessage());
            $loggerService->logError($message);
        });
        return;
    }

    /**
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    protected function initMail(EventInterface $e)
    {
        /* @var \Secretary\Service\Mail $mailService */
        $mailService = $e->getApplication()->getServiceManager()->get('mail-service');
        /* @var \Zend\EventManager\SharedEventManager $sharedEvents */
        $sharedEvents  = $e->getApplication()->getEventManager()->getSharedManager();
        $identifier    = array(
            'Zend\Mvc\Controller\AbstractActionController',
            'Secretary\Service\Base'
        );
        $sharedEvents->attach($identifier, 'sendMail', function ($e) use ($mailService) {
            $target      = $e->getTarget();
            $mailOptions = $e->getParams();
            $mailService->send($mailOptions, $target);
        });
        $sharedEvents->attach('Zend\Mvc\Application', 'dispatch.error', function ($e) use ($mailService) {
            $exception = $e->getParam('exception');
            if (empty($exception)) {
                return;
            }
            $mailService->send(array('exception' => $exception), 'error');
        });
    }

    /**
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    protected function initTranslation(EventInterface $e)
    {
        /* @var $translator \Zend\I18n\Translator\Translator */
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            __DIR__ . '/../../vendor/zendframework/zendframework/resources/languages/de/Zend_Validate.php',
            'default',
            $translator->getLocale()
        );
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
        return;
    }

    /**
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    protected function initUserRegistrationHook(EventInterface $e)
    {
        /* @var $zfcServiceEvents \Zend\EventManager\EventManager */
        $zfcServiceEvents = $e->getApplication()->getServiceManager()
            ->get('zfcuser_user_service')->getEventManager();
        $userService = $e->getApplication()->getServiceManager()->get('user-service');
        $zfcServiceEvents->attach('register.post', array($userService, 'saveUserRoleAndLocale'));
        return;
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
        return;
    }

}
