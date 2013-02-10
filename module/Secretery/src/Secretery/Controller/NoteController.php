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
use Secretery\Service\Note as NoteService;
use Secretery\Entity\Note as NoteEntity;

/**
 * Note Controller
 *
 * @category Controller
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class NoteController extends ActionController
{
    /**
     * @var \Secretery\Service\Note
     */
    protected $noteService;

    /**
     * @param  \Secretery\Entity\Note $note
     * @param  string                 $action
     * @param  int                    $id
     * @return \Zend\Form\Form
     */
    public function getNoteForm(NoteEntity $note, $action = 'add', $id = null)
    {
        $urlValues = array(
            'controller' => 'note',
            'action'     => $action
        );
        if ($action == 'edit') {
            $urlValues['id'] = $id;
        }
        $url = $this->url()->fromRoute('secretery/note', $urlValues);
        return $this->getNoteService()->getNoteForm($note, $url);
    }

    /**
     * @return \Secretery\Service\Note
     */
    public function getNoteService()
    {
        return $this->noteService;
    }


    /**
     * @param  \Secretery\Service\Note $noteService
     * @return \Secretery\Service\Note
     */
    public function setNoteService(NoteService $noteService)
    {
        $this->noteService = $noteService;
        return $this;
    }

    /**
     * Show info/form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $messages       = $this->flashMessenger()->getCurrentSuccessMessages();
        $msg            = false;
        if (!empty($messages)) {
            $msg = array('success', $messages[0]);
        }
        $this->flashMessenger()->clearMessages();
        $noteCollection = $this->noteService->fetchUserNotes($this->identity->getId());
        return new ViewModel(array(
            'noteCollection' => $noteCollection,
            'msg'            => $msg
        ));
    }

    /**
     * Process add form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $noteRecord = new NoteEntity();
        $form       = $this->getNoteForm($noteRecord);

        if (!$this->getRequest()->isPost()) {
            return new ViewModel(array(
                'noteFormLegend' => 'Create Note',
                'noteForm'       => $form
            ));
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                // Save data
                $this->noteService->saveUserNote(
                    $this->identity,
                    $form->getData()
                );
                // Success msg
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('Note was created successfully')
                );
                // Redirect
                return $this->redirect()->toRoute('secretery/note');
            }
        }

        return new ViewModel(array(
            'noteForm'       => $form,
            'noteFormLegend' => 'Create Note',
            'msg'            => array('error', 'An error occurred')
        ));
    }

    /**
     * View Note
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if (empty($id) || !is_numeric($id)) {
            return $this->redirect()->toRoute('secretery/note');
        }
        // Permission Check
        $permissionCheck = $this->noteService->checkNoteViewPermission(
            $this->identity->getId(),
            $id
        );
        if (false === $permissionCheck) {
            // @todo log stuff here?
            return $this->redirect()->toRoute('secretery/note');
        }

        $viewModel      = new ViewModel();
        $keyRequestForm = $this->getKeyRequestForm($id, 'view');

        // View Vars
        $viewModel->setVariable('showKeyRequestForm', true);
        $viewModel->setVariable('keyRequestForm', $keyRequestForm);

        // Render Key Request form
        if (!$this->getRequest()->isPost()) {
            return $viewModel;
        }

        // Key Request Form Validation
        $keyRequestForm->setData($this->getRequest()->getPost());
        if (!$keyRequestForm->isValid()) {
            return $viewModel;
        }

        // Do Note Encryption
        try {
            $formValues    = $keyRequestForm->getData();
            $noteDecrypted = $this->noteService->doNoteEncryption(
                $id,
                $this->identity->getId(),
                $formValues['key'],
                $formValues['passphrase']
            );
        } catch(\LogicException $e) {
            $viewModel->setVariable('msg', array('error', $e->getMessage()));
            return $viewModel;
        }

        // Success
        $viewModel->setVariable('note', $noteDecrypted['note']);
        $viewModel->setVariable('decrypted', $noteDecrypted['decrypted']);
        $viewModel->setVariable('showKeyRequestForm', false);

        return $viewModel;
    }

    /**
     * Edit Note
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if (empty($id) || !is_numeric($id)) {
            return $this->redirect()->toRoute('secretery/note');
        }
        // Permission Check
        $permissionCheck = $this->noteService->checkNoteEditPermission(
            $this->identity->getId(),
            $id
        );
        if (false === $permissionCheck) {
            // @todo log stuff here?
            return $this->redirect()->toRoute('secretery/note');
        }

        $viewModel      = new ViewModel();
        $keyRequestForm = $this->getKeyRequestForm($id, 'edit');

        // View Vars
        $viewModel->setVariable('showKeyRequestForm', true);
        $viewModel->setVariable('keyRequestForm', $keyRequestForm);

        // Render Key Request form
        if (!$this->getRequest()->isPost()) {
            return $viewModel;
        }

        // Key Request Form Validation
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('key-request'))
        {
            $keyRequestForm->setData($this->getRequest()->getPost());
            if (!$keyRequestForm->isValid()) {
                return new ViewModel($viewModel);
            }
            // Do Note Encryption
            try {
                $formValues    = $keyRequestForm->getData();
                $noteDecrypted = $this->getNoteService()->doNoteEncryption(
                    $id,
                    $this->identity->getId(),
                    $formValues['key'],
                    $formValues['passphrase']
                );
            } catch(\LogicException $e) {
                $viewModel->setVariable('msg', array('error', $e->getMessage()));
                return $viewModel;
            }
        }

        $noteRecord = $this->getNoteService()->fetchNote($id);
        $form       = $this->getNoteForm($noteRecord, 'edit', $id);

        $viewModel->setVariable('noteForm', $form);
        $viewModel->setVariable('noteFormLegend', 'Modify Note');
        $viewModel->setVariable('showKeyRequestForm', false);

        if (!$this->getRequest()->getPost('title')) {
            $form->get('content')->setValue($noteDecrypted['decrypted']);
            return $viewModel;
        }

        if ($this->getRequest()->getPost('title')) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                // Save data
                $this->noteService->updateUserNote(
                    $this->identity,
                    $form->getData()
                );
                // Success msg
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('Note was updated successfully')
                );
                // Redirect
                return $this->redirect()->toRoute('secretery/note');
            }
        }

        $viewModel->setVariable('msg', 'An error occurred');
        return $viewModel;
    }

    /**
     * Delete Note
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function deleteAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if (empty($id) || !is_numeric($id)) {
            return $this->redirect()->toRoute('secretery/note');
        }
        // Permission Check
        $permissionCheck = $this->noteService->checkNoteViewPermission(
            $this->identity->getId(),
            $id
        );
        if (false === $permissionCheck) {
            // @todo log stuff here?
            return $this->redirect()->toRoute('secretery/note');
        }

        $viewModel      = new ViewModel();
        $keyRequestForm = $this->getKeyRequestForm($id, 'delete');

        // View Vars
        $viewModel->setVariable('showKeyRequestForm', true);
        $viewModel->setVariable('keyRequestForm', $keyRequestForm);

        // Render Key Request form
        if (!$this->getRequest()->isPost() && !$this->getRequest()->getQuery('confirm')) {
            return $viewModel;
        }

        // Key Request Form Validation
        $keyRequestForm->setData($this->getRequest()->getPost());
        if (!$keyRequestForm->isValid()) {
            return $viewModel;
        }

        // Do Note Encryption
        try {
            $formValues    = $keyRequestForm->getData();
            $noteDecrypted = $this->noteService->doNoteEncryption(
                $id,
                $this->identity->getId(),
                $formValues['key'],
                $formValues['passphrase']
            );
        } catch(\LogicException $e) {
            $viewModel->setVariable('msg', array('error', $e->getMessage()));
            return $viewModel;
        }

        // Delete note
        if ($this->getRequest()->getPost('confirm')) {
            $this->noteService->deleteUserNote($this->identity->getId(), $id);
            // Success msg
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('Note was removed successfully')
            );
            return $this->redirect()->toRoute('secretery/note');
        }

        // Success
        $viewModel->setVariable('note', $noteDecrypted['note']);
        $viewModel->setVariable('decrypted', $noteDecrypted['decrypted']);
        $viewModel->setVariable('showKeyRequestForm', false);

        return $viewModel;
    }

    /**
     * @param  int    $id
     * @param  string $action
     * @return \Secretery\Form\KeyRequest
     */
    protected function getKeyRequestForm($id, $action = 'view')
    {
        $formUrl = $this->url()->fromRoute('secretery/note', array(
            'action' => $action,
            'id'     => $id
        ));
        return $this->noteService->getKeyRequestForm($formUrl);
    }
}
