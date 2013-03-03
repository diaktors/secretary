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
 * @category Controller
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
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
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
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
