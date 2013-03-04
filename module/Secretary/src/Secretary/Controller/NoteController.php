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

use Secretary\Entity\Note as NoteEntity;
use Secretary\Mvc\Controller\ActionController;
use Secretary\Service\Note as NoteService;
use Secretary\Service\Group as GroupService;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

/**
 * Note Controller
 *
 * @category Controller
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class NoteController extends ActionController
{
    /**
     * @var \Secretary\Service\Note
     */
    protected $noteService;

    /**
     * @var \Secretary\Service\Group
     */
    protected $groupService;

    /**
     * @return \Secretary\Service\Note
     */
    public function getNoteService()
    {
        return $this->noteService;
    }


    /**
     * @param  \Secretary\Service\Note $noteService
     * @return \Secretary\Service\Note
     */
    public function setNoteService(NoteService $noteService)
    {
        $this->noteService = $noteService;
        return $this;
    }


    /**
     * @return \Secretary\Service\Group
     */
    public function getGroupService()
    {
        return $this->groupService;
    }


    /**
     * @param  \Secretary\Service\Group $groupService
     * @return \Secretary\Service\Group
     */
    public function setGroupService(GroupService $groupService)
    {
        $this->groupService = $groupService;
        return $this;
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     * @return void
     */
    public function preDispatch(MvcEvent $event)
    {
        parent::preDispatch($event);
        $this->translator->addTranslationFilePattern(
            'gettext', __DIR__ . '/../../../language', 'note-%s.mo'
        );
        $this->getServiceLocator()->get('viewhelpermanager')->get('headScript')
            ->prependFile($this->getRequest()->getBaseUrl() . '/js/note.js', 'text/javascript')
            ->prependFile($this->getRequest()->getBaseUrl() .
                                '/js/vendor/epiceditor/js/epiceditor.min.js', 'text/javascript');

        $userKeyCheck = $this->identity->getKey();
        if (empty($userKeyCheck)) {
            return $this->redirect()->toRoute('secretary/default', array('controller' => 'key'));
        }
    }

    /**
     * Show info/form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $groupId  = null;
        $messages = $this->flashMessenger()->getCurrentSuccessMessages();
        $msg      = false;
        if (!empty($messages)) {
            $msg = array('success', $messages[0]);
        }
        $this->flashMessenger()->clearMessages();
        $userNoteCollection = $this->noteService->fetchUserNotes($this->identity->getId());
        if ($this->getRequest()->getQuery('group') &&
            is_numeric($this->getRequest()->getQuery('group'))) {
            $groupId = (int) $this->getRequest()->getQuery('group');
        }
        $groupNoteCollection = $this->noteService->fetchGroupNotes(
            $this->identity->getId(), $groupId
        );
        return new ViewModel(array(
            'userNoteCollection'  => $userNoteCollection,
            'groupNoteCollection' => $groupNoteCollection,
            'groupCollection'     => $this->identity->getGroups(),
            'groupId'             => $groupId,
            'msg'                 => $msg
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
        $groupForm  = $this->getGroupForm();
        $viewVars   = array(
            'noteFormLegend' => 'Create Note',
            'noteForm'       => $form,
            'groupForm'      => $groupForm
        );

        if (!$this->getRequest()->isPost()) {
            return new ViewModel($viewVars);
        }

        if ($this->getRequest()->isPost()) {
            $viewVars['msg'] = array('error', 'An error occurred');
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                if ($this->getRequest()->getPost('private') == 0) {
                    $groupId = $this->getRequest()->getPost('group');
                    if (empty($groupId) || !is_numeric($groupId)) {
                        // @todo log stuff here?
                        $viewVars['msg'] = array('error', 'You need to select a group');
                        return new ViewModel($viewVars);
                    }
                    $groupMemberCheck = $this->groupService->checkGroupMembership(
                        $groupId, $this->identity->getId()
                    );
                    if (false === $groupMemberCheck) {
                        $this->events->trigger('logViolation', __METHOD__ . '::l42', array(
                            'message' => sprintf('User: %s wants to add note for GroupID: %s',
                                $this->identity->getEmail(),
                                $groupId
                            )
                        ));
                        return new ViewModel($viewVars);
                    }
                }
                if ($this->getRequest()->getPost('private') == 0) {
                    $members = $this->getRequest()->getPost('members');
                    if (empty($members)) {
                        $viewVars['msg'] = array('error', 'Please select a group member to share note with');
                        return new ViewModel($viewVars);
                    }
                    $this->noteService->saveGroupNote(
                        $this->identity,
                        $form->getData(),
                        $groupId,
                        $members
                    );
                } else {
                    $this->noteService->saveUserNote(
                        $this->identity,
                        $form->getData()
                    );
                }
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('Note was created successfully')
                );
                return $this->redirect()->toRoute('secretary/note');
            }
        }

        return new ViewModel($viewVars);
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
            return $this->redirect()->toRoute('secretary/note');
        }
        // Permission Check
        $permissionCheck = $this->noteService->checkNoteViewPermission(
            $this->identity->getId(),
            $id
        );
        if (false === $permissionCheck) {
            // @todo log stuff here?
            return $this->redirect()->toRoute('secretary/note');
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
            return $this->redirect()->toRoute('secretary/note');
        }
        // Permission Check
        $permissionCheck = $this->noteService->checkNoteEditPermission(
            $this->identity->getId(),
            $id
        );
        if (false === $permissionCheck) {
            // @todo log stuff here?
            return $this->redirect()->toRoute('secretary/note');
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

        $noteRecord   = $this->getNoteService()->fetchNote($id);
        $groupMembers = null;
        if (false === $noteRecord->getPrivate()) {
            $groupMembers = $this->groupService->fetchNoteGroupMembers(
                $noteRecord->getId(),
                $noteRecord->getGroup()->getId(),
                $this->identity->getId()
            );
            $viewModel->setVariable('groupMembers', $groupMembers);
            $groupMembersUnselected = $this->groupService->fetchNoteGroupMembersUnselected(
                $noteRecord->getId(),
                $noteRecord->getGroup()->getId(),
                $this->identity->getId()
            );
            $viewModel->setVariable('groupMembersUnselected', $groupMembersUnselected);
        }

        $form = $this->getNoteForm($noteRecord, 'edit', $id, $groupMembers);

        $viewModel->setVariable('noteForm', $form);
        $viewModel->setVariable('editMode', true);
        $viewModel->setVariable('noteRecord', $noteRecord);
        $viewModel->setVariable('noteFormLegend', 'Modify Note');
        $viewModel->setVariable('showKeyRequestForm', false);

        if (!$this->getRequest()->getPost('title')) {
            $form->get('content')->setValue($noteDecrypted['decrypted']);
            return $viewModel;
        }

        if ($this->getRequest()->getPost('title')) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $group = $this->getRequest()->getPost('group');
                if (!empty($group)) {
                    if ($group != $noteRecord->getGroup()->getId()) {
                        $this->events->trigger('logViolation', __METHOD__ . '::l42', array(
                            'message' => sprintf('User: %s wants to change GroupID from: %s to: %s',
                                $this->identity->getEmail(),
                                $noteRecord->getGroup()->getId(),
                                $group
                            )
                        ));
                        $viewVars['msg'] = array('error', 'You cannot change the group');
                        return new ViewModel($viewVars);
                    }
                    $members = $this->getRequest()->getPost('members');
                    if (empty($members)) {
                        $viewVars['msg'] = array('error', 'Please select a group member to share note with');
                        return new ViewModel($viewVars);
                    }
                    $this->noteService->updateGroupNote(
                        $this->identity,
                        $form->getData(),
                        $noteRecord->getGroup()->getId(),
                        $members
                    );
                } else {
                    $this->noteService->updateUserNote(
                        $this->identity,
                        $form->getData()
                    );
                }
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('Note was updated successfully')
                );
                return $this->redirect()->toRoute('secretary/note');
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
            return $this->redirect()->toRoute('secretary/note');
        }
        // Permission Check
        $permissionCheck = $this->noteService->checkNoteViewPermission(
            $this->identity->getId(),
            $id
        );
        if (false === $permissionCheck) {
            $this->events->trigger('logViolation', __METHOD__ . '::l42', array(
                'message' => sprintf('User: %s wants to delete note: %s',
                    $this->identity->getEmail(),
                    $id
                )
            ));
            return $this->redirect()->toRoute('secretary/note');
        }

        $viewModel      = new ViewModel();
        $keyRequestForm = $this->getKeyRequestForm($id, 'delete');

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

        // Delete note
        if ($this->getRequest()->getPost('confirm')) {
            $this->noteService->deleteUserNote($this->identity->getId(), $id);
            // Success msg
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('Note was removed successfully')
            );
            return $this->redirect()->toRoute('secretary/note');
        }


        // Change settings of key request form
        $keyRequestForm->get('key-request')->setName('confirm');
        $keyRequestForm->get('submit')->setValue('Delete note');
        $keyRequestForm->get('passphrase')->setValue('');

        // Show delete verification form
        $viewModel->setVariable('note', $noteDecrypted['note']);
        $viewModel->setVariable('decrypted', $noteDecrypted['decrypted']);
        $viewModel->setVariable('showKeyRequestForm', false);

        return $viewModel;
    }

    /**
     * Return group members in JSON
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function groupAction()
    {
        $jsonModel = new JsonModel(array('success' => false));
        $groupId   = $this->getRequest()->getPost('group');
        if (empty($groupId) || !is_numeric($groupId)) {
            return $jsonModel;
        }
        $groupMembers = $this->groupService->fetchGroupMembers($groupId, $this->identity->getId());
        $jsonModel->setVariable('success', true);
        $jsonModel->setVariable('groupMembers', $groupMembers);
        return $jsonModel;
    }

    /**
     * @param  string $action
     * @param  int    $id
     * @return \Zend\Form\Form
     */
    protected function getGroupForm($id = null)
    {
        $routeParams = array('action' => 'group');
        if (!empty($id)) {
            $routeParams['id'] = $id;
        }
        $url = $this->url()->fromRoute('secretary/note', $routeParams);
        return $this->noteService->getGroupForm($this->identity->getId(), $url);
    }

    /**
     * @param  int    $id
     * @param  string $action
     * @return \Secretary\Form\KeyRequest
     */
    protected function getKeyRequestForm($id, $action = 'view')
    {
        $formUrl = $this->url()->fromRoute('secretary/note', array(
            'action' => $action,
            'id'     => $id
        ));
        return $this->noteService->getKeyRequestForm($formUrl);
    }

    /**
     * @param  \Secretary\Entity\Note $note
     * @param  string                 $action
     * @param  int                    $noteId
     * @param  array                  $groupMembers
     * @return \Zend\Form\Form
     */
    protected function getNoteForm(NoteEntity $note, $action = 'add', $noteId = null, $groupMembers = null)
    {
        $urlValues = array(
            'controller' => 'note',
            'action'     => $action
        );
        if ($action == 'edit') {
            $urlValues['id'] = $noteId;
        }
        $url = $this->url()->fromRoute('secretary/note', $urlValues);
        return $this->getNoteService()->getNoteForm($note, $url, $action, $groupMembers);
    }
}
