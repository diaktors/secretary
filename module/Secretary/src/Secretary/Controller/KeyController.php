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
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Controller;

use Secretary\Entity\Key as KeyEntity;
use Secretary\Form\Key as KeyForm;
use Secretary\Mvc\Controller\ActionController;
use SecretaryCrypt\Crypt as CryptService;
use Secretary\Service\Key as KeyService;
use Secretary\Service\User as UserService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Key Controller
 *
 * @category Controller
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 *
 * @method Request getRequest()
 */
class KeyController extends ActionController
{
    /**
     * @var array
     */
    protected $availableActions = array('create', 'add');

    /**
     * @var KeyForm
     */
    protected $keyForm;

    /**
     * @var CryptService
     */
    protected $cryptService;

    /**
     * @var KeyService
     */
    protected $keyService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param  KeyForm $keyForm
     * @return self
     */
    public function setKeyForm(KeyForm $keyForm)
    {
        $this->keyForm = $keyForm;

        return $this;
    }

    /**
     * @param  KeyService $keyService
     * @return self
     */
    public function setKeyService(KeyService $keyService)
    {
        $this->keyService = $keyService;

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
     * @param  CryptService $cryptService
     * @return self
     */
    public function setCryptService(CryptService $cryptService)
    {
        $this->cryptService = $cryptService;

        return $this;
    }

    /**
     * @return KeyForm
     */
    public function getKeyForm()
    {
        if (is_null($this->keyForm)) {
            $url = $this->url()->fromRoute('secretary/default', array(
                'controller' => 'key',
                'action' => 'add'
            ));
            $this->keyForm = new KeyForm($url);
        }

        return $this->keyForm;
    }

    /**
     * @return CryptService
     */
    public function getCryptService()
    {
        return $this->cryptService;
    }

    /**
     * @return KeyService
     */
    public function getKeyService()
    {
        return $this->keyService;
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
            'gettext', __DIR__ . '/../../../language', 'key-%s.mo'
        );
    }

    /**
     * Show info/form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $keyRecord = $this->getKeyService()->fetchKey($this->identity->getId());
        $keyForm   = false;
        $action    = false;
        if (empty($keyRecord)) {
            $action = $this->params()->fromQuery('action', 'create');
            if (!in_array($action, $this->availableActions)) {
                $action = 'create';
            }
            $keyForm = $this->getKeyForm();
            $keyForm->setMode($action);
        }
        return new ViewModel(array(
            'keyRecord'  => $keyRecord,
            'keyForm'    => $keyForm,
            'activeMode' => $action
        ));
    }

    /**
     * Process add form
     *
     * @return \Zend\View\Model\ViewModel
     * @throws \LogicException If creation of key fails
     */
    public function addAction()
    {
        $keyRecord = $this->getKeyService()->fetchKey($this->identity->getId());
        if (!empty($keyRecord)) {
            return $this->redirect()->toRoute('secretary/default', array(
                'controller' => 'key',
                'action'     => 'index'
            ));
        }

        $action = $this->params()->fromPost('mode', 'create');
        if (!in_array($action, $this->availableActions)) {
            $action = 'create';
        }

        $form = $this->getKeyForm();
        $form->setMode($action);

        $viewModel = new ViewModel();
        $msg       = array('error', 'An error occurred');
        $viewVars  = array(
            'keyRecord'  => $keyRecord,
            'keyForm'    => $form,
            'msg'        => $msg,
            'activeMode' => $action
        );

        if ($this->getRequest()->isPost()) {
            $newKeyRecord = new KeyEntity();
            if ($action == 'create') {
                $form->setInputFilter($newKeyRecord->getInputFilter());
            }
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $values = $form->getData();

                // Generate Keys
                $successText = '';
                $keys = array();
                if ($action == 'create') {
                    $passphrase = $values['passphrase'];
                    try {
                        $keys = $this->getCryptService()->createPrivateKey($passphrase);
                    } catch (\Exception $e) {
                        throw new \LogicException($e->getMessage(), 0, $e);
                    }
                    $viewVars['privKey'] = $keys['priv'];
                    $successText = 'Your key was created successfully';
                }
                // Add own key
                elseif ($action == 'add') {
                    try {
                        $this->getCryptService()->validatePublicKey($values['public_key']);
                    } catch (\Exception $e) {
                        // Error
                        $viewVars['msg'] = array(
                            'error',
                            $this->translator->translate('Validation of provided key failed!')
                        );
                        $viewModel->setVariables($viewVars);
                        $viewModel->setTemplate('secretary/key/index');
                        return $viewModel;
                    }
                    $keys['pub'] = $values['public_key'];
                    $successText = 'Your key was added successfully';
                }

                // Save Data
                $this->getKeyService()->saveKey(
                    $newKeyRecord,
                    $this->identity,
                    $keys['pub']
                );

                // Upgrade User to KeyUser Role
                $this->userService->updateUserToKeyRole($this->identity);

                // Success
                $viewVars['msg'] = array(
                    'success',
                    $this->translator->translate($successText)
                );

                $viewModel->setVariables($viewVars);
                $viewModel->setTemplate('secretary/key/success');
                return $viewModel;
            }
        }

        $viewModel->setVariables($viewVars);
        $viewModel->setTemplate('secretary/key/index');
        return $viewModel;
    }
}
