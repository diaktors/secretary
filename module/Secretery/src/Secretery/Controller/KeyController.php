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

use Secretery\Mvc\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Secretery\Service\Encryption as EncryptionService;
use Secretery\Service\Key as KeyService;
use Secretery\Form\Key as KeyForm;
use Secretery\Entity\Key as KeyEntity;

/**
 * Key Controller
 *
 * @category Controller
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class KeyController extends ActionController
{
    /**
     * @var KeyForm
     */
    protected $keyForm;

    /**
     * @var KeyService
     */
    protected $keyService;

    /**
     * @var EncryptionService
     */
    protected $encryptionService;

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
     * @param  EncryptionService $encryptionService
     * @return self
     */
    public function setEncryptionService(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
        return $this;
    }

    /**
     * @return KeyForm
     */
    public function getKeyForm()
    {
        if (is_null($this->keyForm)) {
            $url = $this->url()->fromRoute('secretery/default', array(
                'controller' => 'key',
                'action' => 'add'
            ));
            $this->keyForm = new KeyForm($url);
        }
        return $this->keyForm;
    }

    /**
     * @return EncryptionService
     */
    public function getEncryptionService()
    {
        return $this->encryptionService;
    }

    /**
     * @return KeyService
     */
    public function getKeyService()
    {
        return $this->keyService;
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
        if (empty($keyRecord)) {
            $keyForm = $this->getKeyForm();
        }
        return new ViewModel(array(
            'keyRecord' => $keyRecord,
            'keyForm'   => $keyForm
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
            return $this->redirect()->toRoute('secretery/default', array(
                'controller' => 'key',
                'action'     => 'index'
            ));
        }
        $form      = $this->getKeyForm();
        $viewModel = new ViewModel();
        $msg       = array('error', 'An error occurred');
        $viewVars  = array(
            'keyRecord' => $keyRecord,
            'keyForm'   => $form,
            'msg'       => $msg
        );

        if ($this->getRequest()->isPost()) {
            $newKeyRecord = new KeyEntity();
            $form->setInputFilter($newKeyRecord->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $values     = $form->getData();
                $passphrase = $values['passphrase'];

                // Generate Keys
                try {
                    $keys = $this->getEncryptionService()->createPrivateKey($passphrase);
                } catch (\Exception $e) {
                    throw new \LogicException($e->getMessage(), 0, $e);
                }

                // Save Data
                $this->getKeyService()->saveKey(
                    $newKeyRecord,
                    $this->identity,
                    $keys['pub']
                );

                // Success
                $viewVars['msg'] = array(
                    'success',
                    $this->translator->translate('Your key was created successfully')
                );
                $viewVars['privKey'] = $keys['priv'];
                $viewModel->setVariables($viewVars);
                $viewModel->setTemplate('secretery/key/success');
                return $viewModel;
            }
        }

        $viewModel->setVariables($viewVars);
        $viewModel->setTemplate('secretery/key/index');
        return $viewModel;
    }
}
