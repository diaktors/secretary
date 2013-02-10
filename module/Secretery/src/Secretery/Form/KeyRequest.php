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
 * KeyRequest Form
 *
 * @category Form
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class KeyRequest extends Form
{
    /**
     * @var InputFilter
     */
    protected $inputFilter;

    public function __construct($action = '#')
    {
        parent::__construct('keyRequestForm');
        $this->setAttribute('method', 'post')
            ->setAttribute('action', $action);
        $this->add(array(
            'name' => 'key',
            'attributes' => array(
                'type'   => 'textarea',
                'required' => true
            ),
            'options'    => array(
                'label'  => 'Your private key',
            ),
        ));
        $this->add(array(
            'name' => 'passphrase',
            'attributes' => array(
                'type'   => 'password',
                'required' => true
            ),
            'options'    => array(
                'label'  => 'Your key passphrase',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'   => 'submit',
                'value'  => 'Send key',
                'class'  => 'btn btn-danger'
            ),
        ));
        $this->add(array(
            'name' => 'key-request',
            'attributes' => array(
                'type'   => 'hidden',
                'required' => true,
                'value' => 1
            ),
        ));

        $this->setInputFilter($this->getInputFilter());
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter  = new InputFilter();
            $inputFactory = new InputFactory();

            $inputFilter->add($inputFactory->createInput(array(
                'name'     => 'key',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            )));

            $inputFilter->add($inputFactory->createInput(array(
                'name'     => 'passphrase',
                'required' => true,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
