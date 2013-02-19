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

use Secretery\Form\Group as GroupForm;
use Secretery\Form\GroupMember as GroupMemberForm;
use Secretery\Entity\Group as GroupEntity;
use Secretery\Mvc\Controller\ActionController;
use Secretery\Service\Group as GroupService;
use Secretery\Service\Note as NoteService;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Group Controller
 *
 * @category Controller
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class GroupController extends ActionController
{
    /**
     * @var \Secretery\Form\Group
     */
    protected $groupForm;

    /**
     * @var int
     */
    protected $groupId;

    /**
     * @var \Secretery\Form\GroupMember
     */
    protected $groupMemberForm;

    /**
     * @var \Secretery\Entity\Group
     */
    protected $groupRecord;

    /**
     * @var \Secretery\Service\Group
     */
    protected $groupService;

    /**
     * @var \Secretery\Service\Note
     */
    protected $noteService;

    /**
     * @var array
     */
    protected $msg;

    /**
     * @param  \Secretery\Form\Group $groupForm
     * @return self
     */
    public function setGroupForm(GroupForm $groupForm)
    {
        $this->groupForm = $groupForm;
        return $this;
    }

    /**
     * @param \Secretery\Form\GroupMember $groupMemberForm
     * @return self
     */
    public function setGroupMemberForm(GroupMemberForm $groupMemberForm)
    {
        $this->groupMemberForm = $groupMemberForm;
        return $this;
    }

    /**
     * @param  \Secretery\Service\Group $groupService
     * @return self
     */
    public function setGroupService(GroupService $groupService)
    {
        $this->groupService = $groupService;
        return $this;
    }

    /**
     * @param  \Secretery\Service\Note $noteService
     * @return self
     */
    public function setNoteService(NoteService $noteService)
    {
        $this->noteService = $noteService;
        return $this;
    }

    /**
     * @param  string $action
     * @param  int    $id
     * @return \Secretery\Form\Group
     */
    public function getGroupForm($action = 'add', $id = null)
    {
        if (is_null($this->groupForm)) {
            $routeParams = array('action' => $action);
            if ($action == 'edit') {
                $routeParams['id'] = $id;
            }
            $url = $this->url()->fromRoute('secretery/group', $routeParams);
            $this->groupForm = new GroupForm($url, $action);
        }
        return $this->groupForm;
    }


    /**
     * @param  int $groupId
     * @return \Secretery\Form\GroupMember
     */
    public function getGroupMemberForm($groupId)
    {
        if (is_null($this->groupForm)) {
            $routeParams = array('action' => 'members', 'id' => $groupId);
            $url = $this->url()->fromRoute('secretery/group', $routeParams);

            //$form = $this->getServiceLocator()->get('FormElementManager')
            //    ->get('Secretery\Form\GroupMember');
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
     * @return \Secretery\Service\Group
     */
    public function getGroupService()
    {
        return $this->groupService;
    }

    /**
     * @return \Secretery\Service\Note
     */
    public function getNoteService()
    {
        return $this->noteService;
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
                return $this->redirect()->toRoute('secretery/group');
            }
            $this->groupRecord = $this->groupService->fetchGroup($this->groupId);
            if (empty($this->groupRecord)) {
                return $this->redirect()->toRoute('secretery/group');
            }
        }
        if ($action == 'index' || $action == 'members' || $action == 'edit') {
            $messages  = $this->flashMessenger()->getCurrentSuccessMessages();
            $this->msg = false;
            if (!empty($messages)) {
                $this->msg = array('success', $messages[0]);
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
                return $this->redirect()->toRoute('secretery/group');
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
            return $this->redirect()->toRoute('secretery/group');
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
                return $this->redirect()->toRoute('secretery/group');
            }

            $viewVars['msg'] = array('error', 'An error occurred');
        }

        $viewModel->setVariables($viewVars);
        $viewModel->setTemplate('secretery/group/index');
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

        if ($this->identity->getId() == $this->groupRecord->getOwner()) {

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
                    return $this->redirect()->toRoute('secretery/group', array(
                        'action' => 'members',
                        'id'     => $this->groupRecord->getId()
                    ));
                }

                $viewVars['msg'] = array(
                    'error', $this->translator->translate('An error occurred')
                );
            }
        }

        $viewModel->setVariables($viewVars);
        $viewModel->setTemplate('secretery/group/index');
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
                return $this->redirect()->toRoute('secretery/group');
            }
            try {
                $groupName = $this->groupRecord->getName();
                $this->noteService->deleteUserFromGroupNotes(
                    $this->identity, $this->groupRecord
                );
                $this->groupService->deleteUserGroup($this->identity, $this->groupRecord);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('Group "%s" was leaved successfully'),
                        $groupName
                    )
                );
                return $this->redirect()->toRoute('secretery/group');
            } catch (\Exception $e) {
                $viewVars['msg'] = array('error', 'An error occurred: ' . $e->getMessage());
            }
        }

        return $viewModel;
    }

}
