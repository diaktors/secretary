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
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

/**
 * User Form
 *
 * @category Form
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class User extends Form
{
    /**
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * @param \Secretery\Entity\User $user
     * @param string                 $action
     */
    public function __construct(\Secretery\Entity\User $user, $action = '#')
    {
        parent::__construct('userForm');
        $this->setAttribute('method', 'post')
            ->setAttribute('action', $action)
            ->setAttribute('class', 'form-horizontal');

        $displayName = new \Zend\Form\Element\Text('display_name');
        $displayName->setAttribute('required', 'required')
            ->setAttribute('label', 'Display Name')
            ->setValue($user->getDisplayName());
        $this->add($displayName);

        $select = new \Zend\Form\Element\Select('language');
        $select->setAttribute('required', 'required')
            ->setAttribute('label', 'Select Language')
            ->setValueOptions(array('de_DE' => 'german', 'en_US' => 'english'))
            ->setValue($user->getLanguage());
        $this->add($select);

        $notifications = new \Zend\Form\Element\Select('notifications');
        $notifications->setAttribute('required', 'required')
            ->setAttribute('label', 'Enable notifications')
            ->setValueOptions(array('0' => 'no', '1' => 'yes'))
            ->setValue($user->getNotifications());
        $this->add($notifications);

        $submit = new \Zend\Form\Element\Submit('submit');
        $submit->setAttribute('class', 'btn btn-primary')
            ->setAttribute('value', 'save');
        $this->add($submit);
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter  = new InputFilter();
            $inputFactory = new InputFactory();
            $inputFilter->add($inputFactory->createInput(array(
                'name'     => 'display_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array('name' => 'Alnum', 'options' => array('allowWhiteSpace' => true)),
                ),
            )));
            $inputFilter->add($inputFactory->createInput(array(
                'name'     => 'language',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array('name' => 'StringLength', 'options' => array('min' => 5, 'max' => 5)),
                ),
            )));
            $inputFilter->add($inputFactory->createInput(array(
                'name'     => 'notifications',
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
