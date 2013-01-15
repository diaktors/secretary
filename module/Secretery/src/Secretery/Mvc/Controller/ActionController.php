<?php
/**
 * Wesrc Copyright 2013
 * Modifying, copying, of code contained herein that is not specifically
 * authorized by Wesrc UG ("Company") is strictly prohibited.
 * Violators will be prosecuted.
 *
 * This restriction applies to proprietary code developed by WsSrc. Code from
 * third-parties or open source projects may be subject to other licensing
 * restrictions by their respective owners.
 *
 * Additional terms can be found at http://www.wesrc.com/company/terms
 *
 * PHP Version 5
 *
 * @category Mvc
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Mvc\Controller;

use Zend\Mvc\MvcEvent,
    Zend\View\Model\ViewModel;

/**
 * MVC Action Controller
 *
 * @category Mvc
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class ActionController extends \Zend\Mvc\Controller\AbstractActionController
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var \Secretery\Entity\User
     */
    protected $identity;

    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @return void
     */
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();

        $this->events->attach('dispatch', array($this, 'preDispatch') ,  100);
        $this->events->attach('dispatch', array($this, 'postDispatch'), -100);
    }

    /**
     * @return void
     */
    public function preDispatch(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();

        /**
         * Fetch Identity
         * @var $zfcUserAuthentication \ZfcUser\Controller\Plugin\ZfcUserAuthentication
         */
        $zfcUserAuthentication = $this->getPluginManager()->get('zfcUserAuthentication');
        if ($zfcUserAuthentication->hasIdentity()) {
            $this->identity = $zfcUserAuthentication->getIdentity();
        } elseif ($routeMatch->getMatchedRouteName() != 'home') {
            return $this->redirect()->toRoute('home');
        }

        // Locale
        $locale = $routeMatch->getParam('locale', 'de_DE');
        if (0 === strlen($locale)) {
            throw new InvalidArgumentException('No locale has been specified.');
        }
        $this->locale = $locale;

        // View Model
        if (null === $this->view) {
            $this->view = new ViewModel();
        }

        // Call View Model Variables
        $this->setViewModelVariablesInternationalization($event)
            ->setViewModelVariablesMvc($event)
            ->setViewModelVariablesUri($event);

    }

    public function postDispatch(MvcEvent $event)
    {
    }


    /**
     * Pass language and locale related variables to ViewModel
     *
     * @param  MvcEvent $event
     * @return \Secretery\Mvc\Controller\ActionController
     */
    protected function setViewModelVariablesInternationalization(MvcEvent $event)
    {
        $this->view->setVariable('locale', $this->locale);

        $dateTimezone = new \DateTimeZone('Europe/Berlin');
        $this->view->setVariable('date', new \DateTime('now', $dateTimezone));

        $dateFormat           = new \StdClass();
        $dateFormat->time     = 'H:i:s';
        $dateFormat->date     = 'Y-m-d';
        $dateFormat->datetime = 'Y-m-d H:i:s';

        $this->view->setVariable('dateFormat', $dateFormat);

        return $this;
    }

    /**
     * Pass MVC related variables to ViewModel
     *
     * @param  MvcEvent $event
     * @return \Secretery\Mvc\Controller\ActionController
     */
    protected function setViewModelVariablesMvc(MvcEvent $event)
    {
        //$this->view->setVariable('module',     $event->getRouteMatch()->getParam('module'));
        $this->view->setVariable('controller', $event->getRouteMatch()->getParam('controller'));
        $this->view->setVariable('action',     $event->getRouteMatch()->getParam('action'));

        $layoutView = $this->getEvent('application')->getViewModel();
        $layoutView->routeMatch = $event->getRouteMatch()->getMatchedRouteName();

        return $this;
    }

    /**
     * Pass Request Uri related variables to ViewModel
     *
     * @param MvcEvent $event
     * @return \Secretery\Mvc\Controller\ActionController
     */
    protected function setViewModelVariablesUri(MvcEvent $event)
    {
        $uri = new \StdClass();

        $uri->absolute = $this->getRequest()->getUri();
        $uri->relative = $this->getRequest()->getRequestUri();

        $uri->scheme   = parse_url($uri->absolute, PHP_URL_SCHEME);
        $uri->host     = parse_url($uri->absolute, PHP_URL_HOST);
        $uri->port     = parse_url($uri->absolute, PHP_URL_PORT);
        $uri->user     = parse_url($uri->absolute, PHP_URL_USER);
        $uri->password = parse_url($uri->absolute, PHP_URL_PASS);
        $uri->path     = parse_url($uri->absolute, PHP_URL_PATH);
        $uri->query    = parse_url($uri->absolute, PHP_URL_QUERY);

        $this->view->setVariable('uri', $uri);

        return $this;
    }


}