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
use Secretery\Service\Key as KeyService;
use Secretery\Mapper\Key as KeyMapper;
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
     * @var KeyMapper
     */
    protected $keyMapper;

    /**
     * @var KeyService
     */
    protected $keyService;

    /**
     * @param KeyForm $keyForm
     */
    public function setKeyForm(KeyForm $keyForm)
    {
        $this->keyForm = $keyForm;
        return $this;
    }

    /**
     * @param KeyMapper $keyService
     */
    public function setKeyMapper(KeyMapper $keyMapper)
    {
        $this->keyMapper = $keyMapper;
        return $this;
    }

    /**
     * @param KeyService $keyService
     */
    public function setKeyService(KeyService $keyService)
    {
        $this->keyService = $keyService;
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
     * @return KeyService
     */
    public function getKeyService()
    {
        return $this->keyService;
    }

    /**
     * @return KeyMapper
     */
    public function getKeyMapper()
    {
        return $this->keyMapper;
    }

    /**
     * Show info/form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $keyRecord = $this->keyMapper->fetchKey($this->identity->getId());
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
     */
    public function addAction()
    {
        $keyRecord = $this->keyMapper->fetchKey($this->identity->getId());
        if (!empty($keyRecord)) {
            return $this->redirect()->toRoute('secretery/default', array(
                'controller' => 'key',
                'action'     => 'index'
            ));
        }
        $form      = $this->getKeyForm();
        $request   = $this->getRequest();
        $viewModel = new ViewModel();
        $msg       = array('error', 'An error occurred');
        $viewVars  = array(
            'keyRecord' => $keyRecord,
            'keyForm'   => $form,
            'msg'       => $msg
        );

        if ($request->isPost()) {
            $newKeyRecord = new KeyEntity();
            $form->setInputFilter($newKeyRecord->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $values     = $form->getData();
                $passphrase = $values['passphrase'];

                // Generate Keys
                try {
                    $keys = $this->keyService->createPrivateKey($passphrase);
                } catch (\Exception $e) {
                    throw new \LogicException($e->getMessage(), null, $e);
                }

                // Save Data
                $this->keyMapper->saveKey(
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
