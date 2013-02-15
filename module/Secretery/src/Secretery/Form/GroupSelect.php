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
 * @category Form
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Form;

use Zend\Form\Form;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

/**
 * Group Select Form
 *
 * @category Form
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class GroupSelect extends Form implements ObjectManagerAwareInterface
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;


    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }


    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int    $userId
     * @param string $action
     */
    public function __construct($userId, $action = '#')
    {
        $this->setUserId($userId);
        parent::__construct('groupSelectForm');
        $this->setAttribute('method', 'post')
            ->setAttribute('action', $action)
            ->setAttribute('class', 'form-inline');
    }

    /**
     * @throws \InvalidArgumentException If $userId is empty
     */
    public function init()
    {
        $this->add(array(
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'group',
            'options' => array(
                'object_manager' => $this->getObjectManager(),
                'target_class'   => 'Secretery\Entity\Group',
                'property'       => 'name',
                'find_method'    => array(
                    'name'   => 'fetchUserGroups',
                    'params' => array('userId' => $this->userId),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'   => 'submit',
                'value'  => 'select',
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
                'name'     => 'group',
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

}
