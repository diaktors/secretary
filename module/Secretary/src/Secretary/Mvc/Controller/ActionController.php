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
 * @category Mvc
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Mvc\Controller;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * MVC Action Controller
 *
 * @category Mvc
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class ActionController extends \Zend\Mvc\Controller\AbstractActionController
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var \Secretary\Entity\User
     */
    protected $identity;

    /**
     * @var \Zend\i18n\Translator\Translator
     */
    protected $translator;

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

        // Translator
        $this->translator = $this->getServiceLocator()->get('translator');

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
     * @return \Secretary\Mvc\Controller\ActionController
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
     * @return \Secretary\Mvc\Controller\ActionController
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
     * @return \Secretary\Mvc\Controller\ActionController
     */
    protected function setViewModelVariablesUri(MvcEvent $event)
    {
        $uri = new \StdClass();

        $uri->absolute = $this->getRequest()->getUri();
        //$uri->relative = $this->getRequest()->getRequestUri();

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