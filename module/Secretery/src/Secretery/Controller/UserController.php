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
 * @category Controller
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Controller;

use Secretery\Form\User as UserForm;
use Secretery\Mvc\Controller\ActionController;
use Secretery\Service\User as UserService;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * User Controller
 *
 * @category Controller
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class UserController extends ActionController
{
    /**
     * @var \Secretery\Form\User
     */
    protected $userForm;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param  UserForm $userForm
     * @return self
     */
    public function setUserForm(UserForm $userForm)
    {
        $this->userForm = $userForm;
        return $this;
    }

    /**
     * @param  UserService $userService
     * @return self
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }

    /**
     * @return \Secretery\Form\User
     */
    public function getUserForm()
    {
        if (is_null($this->userForm)) {
            $url = $this->url()->fromRoute('secretery/default', array(
                'controller' => 'user',
                'action' => 'settings'
            ));
            $this->userForm = new UserForm($this->identity, $url);
        }
        return $this->userForm;
    }

    /**
     * @return UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function preDispatch(MvcEvent $event)
    {
        parent::preDispatch($event);
        $this->translator->addTranslationFilePattern(
            'gettext', __DIR__ . '/../../../language', 'user-%s.mo'
        );
    }

    /**
     * User settings
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function settingsAction()
    {
        $form      = $this->getUserForm();
        $viewModel = new ViewModel();
        $viewVars  = array(
            'userForm'  => $form,
        );

        $messages = $this->flashMessenger()->getCurrentSuccessMessages();
        if (!empty($messages)) {
            $viewVars['msg'] = array('success', $messages[0]);
        }
        $this->flashMessenger()->clearMessages();

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $values     = $form->getData();

                // Upgrade User to KeyUser Role
                $this->userService->updateUserSettings($this->identity, $values);

                // Success
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('Your settings were updated')
                );
                return $this->redirect()->toRoute('user-settings');
            }

            $viewVars['msg'] = array('error', 'An error occurred');
        }

        $viewModel->setVariables($viewVars);
        return $viewModel;
    }
}
