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

/**
 * Group Form
 *
 * @category Form
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Group extends Form
{
    public function __construct($action = '#', $mode = 'add')
    {
        parent::__construct('groupForm');
        $this->setAttribute('method', 'post')
            ->setAttribute('action', $action)
            ->setAttribute('class', 'form-horizontal');
        $this->add(array(
            'name' => 'groupname',
            'attributes' => array(
                'type'     => 'text',
                'required' => 'required'
            ),
            'options'    => array(
                'label'  => 'Groupname',
            ),
        ));
        $submitTitle = 'Create Group';
        if ($mode == 'edit') {
            $submitTitle = 'Save Group';
        }
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'   => 'submit',
                'value'  => $submitTitle,
                'class'  => 'btn btn-primary'
            ),
        ));
    }
}
