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
     * @param  \Secretery\Entity\Note $noteRecord
     * @return \Zend\Form\Form
     */
    public function getNoteForm(NoteEntity $note)
    {
        $url = $this->url()->fromRoute('secretery/default', array(
            'controller' => 'note',
            'action' => 'add'
        ));
        return $this->noteService->getNoteForm($note, $url);
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
                'noteForm' => $form
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
                    $this->translator->translate('Your note was created successfully')
                );
                // Redirect
                return $this->redirect()->toRoute('secretery/note');
            }
        }

        return new ViewModel(array(
            'noteForm' => $form,
            'msg'      => array('error', 'An error occurred')
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
        $permissionCheck = $this->getNoteService()->checkNoteViewPermission(
            $this->identity->getId(),
            $id
        );
        if (false === $permissionCheck) {
            // @todo log stuff here?
            return $this->redirect()->toRoute('secretery/note');
        }

        $viewModelVars             = array();
        $viewModelVars['showForm'] = true;

        // Key Request Form
        $formUrl = $this->url()->fromRoute('secretery/note', array(
            'action' => 'view',
            'id'     => $id
        ));
        $keyRequestForm = $this->getNoteService()->getKeyRequestForm($formUrl);
        $viewModelVars['keyRequestForm'] = $keyRequestForm;

        // Key Request Form Validation
        if ($this->getRequest()->isPost()) {
            $keyRequestForm->setData($this->getRequest()->getPost());
            if ($keyRequestForm->isValid()) {
                $values          = $keyRequestForm->getData();
                $validationCheck = $this->getNoteService()->validateKey(
                    $values['key'], $values['passphrase']
                );
                // Show key read error
                if (false === $validationCheck) {
                    $viewModelVars['msg'] = array('error', 'Your key is not readable');
                    return new ViewModel($viewModelVars);
                }
                // Fetch Note
                $noteRecord = $this->getNoteService()->fetchNoteWithUserData(
                    $id, $this->identity->getId()
                );
                $noteDecrypted = $this->getNoteService()->decryptNote(
                    $noteRecord['content'],
                    $noteRecord['eKey'],
                    $values['key'],
                    $values['passphrase']
                );
                // Show key read error
                if (false === $noteDecrypted) {
                    $viewModelVars['msg'] = array('error', 'Note could not be decrypted');
                    return new ViewModel($viewModelVars);
                }
                // Success
                $viewModelVars['showForm']    = false;
                $viewModelVars['noteContent'] = $noteDecrypted;
                $viewModelVars['note']        = $noteRecord;
            }
        }

        return new ViewModel($viewModelVars);
    }
}
