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
 * @category Form
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Form;

use Zend\Form\Form;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

/**
 * Group Member Form
 *
 * @category Form
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class GroupMember extends Form implements ObjectManagerAwareInterface
{

    /**
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $groupId;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    public function __construct($action = '#')
    {
        parent::__construct('groupMemberForm');
        $this->setAttribute('method', 'post')
            ->setAttribute('action', $action)
            ->setAttribute('class', 'form-inline');
    }

    /**
     * @throws \InvalidArgumentException If $userId is empty
     */
    public function init()
    {
        if (empty($this->userId)) {
            throw new \InvalidArgumentException('Please provide Identity ID');
        }
        if (empty($this->groupId)) {
            throw new \InvalidArgumentException('Please provide Group ID');
        }

        $this->add(array(
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'newMember',
            'options' => array(
                'object_manager' => $this->getObjectManager(),
                'target_class'   => 'Secretary\Entity\User',
                'property'       => 'displayName',
                'find_method'    => array(
                    'name'   => 'getSelectUser',
                    'params' => array('userId' => $this->userId, 'groupId' => $this->groupId),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'   => 'submit',
                'value'  => 'add',
                'class'  => 'btn btn-primary'
            ),
        ));
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter  = new InputFilter();
            $inputFactory = new InputFactory();
            $inputFilter->add($inputFactory->createInput(array(
                'name'     => 'newMember',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array('name' => 'Digits'),
                ),
            )));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = (int) $userId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = (int) $groupId;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
