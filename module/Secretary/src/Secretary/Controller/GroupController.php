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

use Secretary\Form\Group as GroupForm;
use Secretary\Form\GroupMember as GroupMemberForm;
use Secretary\Entity\Group as GroupEntity;
use Secretary\Mvc\Controller\ActionController;
use Secretary\Service\Group as GroupService;
use Secretary\Service\Note as NoteService;
use Secretary\Service\User as UserService;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Group Controller
 *
 * @category Controller
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class GroupController extends ActionController
{
    /**
     * @var \Secretary\Form\Group
     */
    protected $groupForm;

    /**
     * @var int
     */
    protected $groupId;

    /**
     * @var \Secretary\Form\GroupMember
     */
    protected $groupMemberForm;

    /**
     * @var \Secretary\Entity\Group
     */
    protected $groupRecord;

    /**
     * @var \Secretary\Service\Group
     */
    protected $groupService;

    /**
     * @var \Secretary\Service\Note
     */
    protected $noteService;

    /**
     * @var \Secretary\Service\User
     */
    protected $userService;

    /**
     * @var array
     */
    protected $msg;

    /**
     * @param  \Secretary\Form\Group $groupForm
     * @return self
     */
    public function setGroupForm(GroupForm $groupForm)
    {
        $this->groupForm = $groupForm;
        return $this;
    }

    /**
     * @param \Secretary\Form\GroupMember $groupMemberForm
     * @return self
     */
    public function setGroupMemberForm(GroupMemberForm $groupMemberForm)
    {
        $this->groupMemberForm = $groupMemberForm;
        return $this;
    }

    /**
     * @param  \Secretary\Service\Group $groupService
     * @return self
     */
    public function setGroupService(GroupService $groupService)
    {
        $this->groupService = $groupService;
        return $this;
    }

    /**
     * @param  \Secretary\Service\Note $noteService
     * @return self
     */
    public function setNoteService(NoteService $noteService)
    {
        $this->noteService = $noteService;
        return $this;
    }

    /**
     * @param  \Secretary\Service\User $userService
     * @return self
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }

    /**
     * @param  string $action
     * @param  int    $id
     * @return \Secretary\Form\Group
     */
    public function getGroupForm($action = 'add', $id = null)
    {
        if (is_null($this->groupForm)) {
            $routeParams = array('action' => $action);
            if ($action == 'edit') {
                $routeParams['id'] = $id;
            }
            $url = $this->url()->fromRoute('secretary/group', $routeParams);
            $this->groupForm = new GroupForm($url, $action);
        }
        return $this->groupForm;
    }


    /**
     * @param  int $groupId
     * @return \Secretary\Form\GroupMember
     */
    public function getGroupMemberForm($groupId)
    {
        if (is_null($this->groupForm)) {
            $routeParams = array('action' => 'members', 'id' => $groupId);
            $url = $this->url()->fromRoute('secretary/group', $routeParams);

            //$form = $this->getServiceLocator()->get('FormElementManager')
            //    ->get('Secretary\Form\GroupMember');
            $form = new GroupMemberForm($url);
            $form->setObjectManager($this->groupService->getEntityManager());
            $form->setUserId($this->identity->getId());
            $form->setGroupId($groupId);
            $form->init();
            $this->groupMemberForm = $form;
        }
        return $this->groupMemberForm;
    }

    /**
     * @return \Secretary\Service\Group
     */
    public function getGroupService()
    {
        return $this->groupService;
    }

    /**
     * @return \Secretary\Service\Note
     */
    public function getNoteService()
    {
        return $this->noteService;
    }

    /**
     * @return \Secretary\Service\User
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

        $action = $event->getRouteMatch()->getParam('action');
        if ($action != 'index' && $action != 'add') {
            $this->groupId = $event->getRouteMatch()->getParam('id');
            if (empty($this->groupId) || !is_numeric($this->groupId)) {
                return $this->redirect()->toRoute('secretary/group');
            }
            $this->groupRecord = $this->groupService->fetchGroup($this->groupId);
            if (empty($this->groupRecord)) {
                return $this->redirect()->toRoute('secretary/group');
            }
        }
        if ($action == 'index' || $action == 'members' || $action == 'edit') {
            $this->msg = false;
            $messages  = $this->flashMessenger()->getCurrentErrorMessages();
            if (!empty($messages)) {
                $this->msg = array('error', $messages[0]);
            } else {
                $messages  = $this->flashMessenger()->getCurrentSuccessMessages();
                $this->msg = false;
                if (!empty($messages)) {
                    $this->msg = array('success', $messages[0]);
                }
            }
            $this->flashMessenger()->clearMessages();
        }

        $this->translator->addTranslationFilePattern(
            'gettext', __DIR__ . '/../../../language', 'group-%s.mo'
        );
    }

    /**
     * Show group list
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'groupCollection' => $this->identity->getGroups(),
            'msg'             => $this->msg,
            'groupForm'       => $this->getGroupForm()
        ));
    }

    /**
     * Process add group form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $form      = $this->getGroupForm();
        $viewModel = new ViewModel();
        $viewVars  = array('groupForm' => $form);

        if ($this->getRequest()->isPost()) {
            $groupRecord = new GroupEntity();
            $form->setInputFilter($groupRecord->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $values      = $form->getData();
                $groupName   = $values['groupname'];
                $groupRecord = $this->groupService->addUserGroup($this->identity, $groupName);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('Group "%s" was created successfully'),
                        $groupRecord->getName()
                    )
                );
                return $this->redirect()->toRoute('secretary/group');
            }

            $viewVars['msg'] = array('error', 'An error occurred');
        }

        $viewModel->setVariables($viewVars);
        return $viewModel;
    }

    /**
     * Process edit group form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        if ($this->identity->getId() != $this->groupRecord->getOwner()) {
            return $this->redirect()->toRoute('secretary/group');
        }

        $groupCollection = $this->identity->getGroups();
        $form      = $this->getGroupForm('edit', $this->groupId);
        $viewModel = new ViewModel();
        $viewVars  = array(
            'groupRecord'     => $this->groupRecord,
            'groupCollection' => $groupCollection,
            'msg'             => $this->msg,
            'groupForm'       => $form,
            'editMode'        => true
        );
        $form->setData(array('groupname' => $this->groupRecord->getName()));

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($this->groupRecord->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $values     = $form->getData();
                $groupName   = $values['groupname'];
                $groupRecord = $this->groupService->updateGroup(
                    $this->groupRecord, $groupName
                );
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('Group "%s" was saved successfully'),
                        $groupRecord->getName()
                    )
                );
                return $this->redirect()->toRoute('secretary/group');
            }

            $viewVars['msg'] = array('error', 'An error occurred');
        }

        $viewModel->setVariables($viewVars);
        $viewModel->setTemplate('secretary/group/index');
        return $viewModel;
    }

    /**
     * Show Group Members
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function membersAction()
    {
        $viewModel = new ViewModel();
        $viewVars  = array(
            'groupRecord'     => $this->groupRecord,
            'memberMode'      => true,
            'groupCollection' => $this->identity->getGroups(),
            'msg'             => $this->msg
        );

        if ($this->identity->getId() != $this->groupRecord->getOwner()) {
            $viewModel->setVariables($viewVars);
            $viewModel->setTemplate('secretary/group/index');
            return $viewModel;
        }

        $this->getServiceLocator()->get('viewhelpermanager')->get('headScript')
            ->prependFile($this->getRequest()->getBaseUrl() . '/js/group.js', 'text/javascript');

        $form = $this->getGroupMemberForm($this->groupRecord->getId());
        $viewVars['newMemberForm'] = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($this->groupRecord->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid() && $this->getRequest()->getPost('newMember') != 0) {
                $values     = $form->getData();
                $newMember  = $values['newMember'];
                $userRecord = $this->groupService->addGroupMember($this->groupRecord, $newMember);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('User "%s" was added to group "%s"'),
                        $userRecord->getDisplayName(),
                        $this->groupRecord->getName()
                    )
                );
                return $this->redirect()->toRoute('secretary/group', array(
                    'action' => 'members',
                    'id'     => $this->groupRecord->getId()
                ));
            }

            $viewVars['msg'] = array(
                'error', $this->translator->translate('An error occurred')
            );
        }

        $viewModel->setVariables($viewVars);
        $viewModel->setTemplate('secretary/group/index');
        return $viewModel;
    }

    /**
     * Leave group
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function leaveAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setVariable('groupRecord', $this->groupRecord);

        // Delete User from Group / Delete Group
        if ($this->getRequest()->getQuery('confirm')) {
            if ($this->groupRecord->getOwner() == $this->identity->getId()) {
                return $this->redirect()->toRoute('secretary/group');
            }
            try {
                $groupName = $this->groupRecord->getName();
                $this->noteService->deleteUserFromGroupNotes(
                    $this->identity, $this->groupRecord
                );
                $this->groupService->removeUserFromGroup($this->identity, $this->groupRecord);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('Group "%s" was leaved successfully'),
                        $groupName
                    )
                );
                return $this->redirect()->toRoute('secretary/group');
            } catch (\Exception $e) {
                $viewVars['msg'] = array('error', 'An error occurred: ' . $e->getMessage());
            }
        }

        return $viewModel;
    }


    /**
     * Remove member from group
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function removeAction()
    {
        $userId = (int) $this->getRequest()->getQuery('user');
        if ($this->groupRecord->getOwner() != $this->identity->getId()) {
            return $this->redirect()->toRoute('secretary/group');
        }
        if (empty($userId) || $userId == $this->identity->getId()) {
            return $this->redirect()->toRoute('secretary/group', array(
                'action' => 'members', 'id' => $this->groupRecord->getId()
            ));
        }

        $membershipCheck = $this->groupService->checkGroupMembership(
            $this->groupRecord->getId(), $userId
        );
        if (false === $membershipCheck) {
            return $this->redirect()->toRoute('secretary/group', array(
                'action' => 'members', 'id' => $this->groupRecord->getId()
            ));
        }

        // Delete User from Group / Delete Group
        try {
            $groupName  = $this->groupRecord->getName();
            $userRecord = $this->userService->getUserById($userId);

            $this->noteService->deleteUserFromGroupNotes(
                $userRecord, $this->groupRecord
            );
            $this->groupService->removeUserFromGroup($userRecord, $this->groupRecord);

            $this->flashMessenger()->addSuccessMessage(
                sprintf(
                    $this->translator->translate('User "%s" was successfully removed from group "%s"'),
                    $userRecord->getDisplayName(),
                    $groupName
                )
            );
            return $this->redirect()->toRoute('secretary/group', array(
                'action' => 'members', 'id' => $this->groupRecord->getId()
            ));
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('An error occurred: ' . $e->getMessage());
            return $this->redirect()->toRoute('secretary/group', array(
                'action' => 'members', 'id' => $this->groupRecord->getId()
            ));
        }
    }

}
