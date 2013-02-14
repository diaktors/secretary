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
use Secretery\Service\Group as GroupService;
use Secretery\Form\Group as GroupForm;
use Secretery\Form\GroupMember as GroupMemberForm;
use Secretery\Entity\Group as GroupEntity;

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
     * @var GroupForm
     */
    protected $groupForm;

    /**
     * @var GroupMemberForm
     */
    protected $groupMemberForm;

    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @param  GroupForm $groupForm
     * @return self
     */
    public function setGroupForm(GroupForm $groupForm)
    {
        $this->groupForm = $groupForm;
        return $this;
    }

    /**
     * @param  GroupMemberForm $groupForm
     * @return self
     */
    public function setGroupMemberForm(GroupMemberForm $groupMemberForm)
    {
        $this->groupMemberForm = $groupMemberForm;
        return $this;
    }

    /**
     * @param  GroupService $groupService
     * @return self
     */
    public function setGroupService(GroupService $groupService)
    {
        $this->groupService = $groupService;
        return $this;
    }

    /**
     * @param  string $action
     * @return GroupForm
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
     * @param  string $action
     * @return GroupMemberForm
     */
    public function getGroupMemberForm($id)
    {
        if (is_null($this->groupForm)) {
            $routeParams = array('action' => 'members', 'id' => $id);
            $url = $this->url()->fromRoute('secretery/group', $routeParams);

            //$form = $this->getServiceLocator()->get('FormElementManager')
            //    ->get('Secretery\Form\GroupMember');
            $form = new GroupMemberForm($url);
            $form->setObjectManager($this->groupService->getEntityManager());
            $form->setUserId($this->identity->getId());
            $form->init();
            $this->groupMemberForm = $form;
        }
        return $this->groupMemberForm;
    }

    /**
     * @return GroupService
     */
    public function getGroupService()
    {
        return $this->groupService;
    }

    /**
     * Show group list
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
        $groupCollection = $this->identity->getGroups();
        $form = $this->getGroupForm();
        $form->setAttribute('class', 'for-vertical');
        return new ViewModel(array(
            'groupCollection' => $groupCollection,
            'msg'             => $msg,
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
                $values    = $form->getData();
                $groupname = $values['groupname'];

                // Save Group
                $groupRecord = $this->groupService->addUserGroup($this->identity, $groupname);

                // Success
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('Group "%s" was created successfully'),
                        $groupRecord->getName()
                    )
                );

                // Redirect
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
        $groupId = $this->getEvent()->getRouteMatch()->getParam('id');
        if (empty($groupId) || !is_numeric($groupId)) {
            return $this->redirect()->toRoute('secretery/group');
        }
        $groupRecord = $this->groupService->fetchGroup($groupId);
        if (empty($groupRecord)) {
            return $this->redirect()->toRoute('secretery/group');
        }
        if ($this->identity->getId() != $groupRecord->getOwner()) {
            return $this->redirect()->toRoute('secretery/group');
        }

        $groupCollection = $this->identity->getGroups();
        $messages       = $this->flashMessenger()->getCurrentSuccessMessages();
        $msg            = false;
        if (!empty($messages)) {
            $msg = array('success', $messages[0]);
        }
        $this->flashMessenger()->clearMessages();

        $form      = $this->getGroupForm('edit', $groupId);
        $viewModel = new ViewModel();
        $viewVars  = array(
            'groupRecord'     => $groupRecord,
            'groupCollection' => $groupCollection,
            'msg'             => $msg,
            'groupForm'       => $form,
            'editMode' => true
        );
        $form->setData(array('groupname' => $groupRecord->getName()));

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($groupRecord->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $values    = $form->getData();
                $groupname = $values['groupname'];

                // Save Group
                $groupRecord = $this->groupService->updateGroup($groupRecord, $groupname);

                // Success
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('Group "%s" was saved successfully'),
                        $groupRecord->getName()
                    )
                );

                // Redirect
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
        $groupId = $this->getEvent()->getRouteMatch()->getParam('id');
        if (empty($groupId) || !is_numeric($groupId)) {
            return $this->redirect()->toRoute('secretery/group');
        }
        $groupRecord = $this->groupService->fetchGroup($groupId);
        if (empty($groupRecord)) {
            return $this->redirect()->toRoute('secretery/group');
        }

        $groupCollection = $this->identity->getGroups();
        $messages       = $this->flashMessenger()->getCurrentSuccessMessages();
        $msg            = false;
        if (!empty($messages)) {
            $msg = array('success', $messages[0]);
        }
        $this->flashMessenger()->clearMessages();

        $viewModel = new ViewModel();
        $viewVars  = array(
            'groupRecord'     => $groupRecord,
            'memberMode'      => true,
            'groupCollection' => $groupCollection,
            'msg'             => $msg
        );


        if ($this->identity->getId() == $groupRecord->getOwner()) {

            $form = $this->getGroupMemberForm($groupRecord->getId());
            $viewVars['newMemberForm'] = $form;

            if ($this->getRequest()->isPost()) {
                $form->setInputFilter($groupRecord->getInputFilter());
                $form->setData($this->getRequest()->getPost());

                if ($form->isValid()) {
                    $values    = $form->getData();
                    $newMember = $values['newMember'];

                    // Save new Group Member
                    $userRecord = $this->groupService->addGroupMember($groupRecord, $newMember);

                    // Success
                    $this->flashMessenger()->addSuccessMessage(
                        sprintf(
                            $this->translator->translate('User "%s" was added to group "%s'),
                            $userRecord->getDisplayName(),
                            $groupRecord->getName()
                        )
                    );

                    // Redirect
                    return $this->redirect()->toRoute('secretery/group', array(
                        'action' => 'members',
                        'id'     => $groupRecord->getId()
                    ));
                }

                $viewVars['msg'] = array('error', 'An error occurred');
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
        $groupId = $this->getEvent()->getRouteMatch()->getParam('id');
        if (empty($groupId) || !is_numeric($groupId)) {
            return $this->redirect()->toRoute('secretery/group');
        }
        $groupRecord = $this->groupService->fetchGroup($groupId);
        if (empty($groupRecord)) {
            return $this->redirect()->toRoute('secretery/group');
        }

        $viewModel = new ViewModel();
        $viewVars  = array('groupRecord' => $groupRecord);

        if ($this->getRequest()->getQuery('confirm')) {

            // Delete User from Group / Delete Group
            try {
                $groupname = $groupRecord->getName();
                $this->groupService->deleteUserGroup($this->identity, $groupRecord);

                // Success
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('Group "%s" was leaved successfully'),
                        $groupname
                    )
                );

                // Redirect
                return $this->redirect()->toRoute('secretery/group');
            } catch (\Exception $e) {
                $viewVars['msg'] = array('error', 'An error occurred: ' . $e->getMessage());
            }
        }

        $viewModel->setVariables($viewVars);
        return $viewModel;
    }

}
