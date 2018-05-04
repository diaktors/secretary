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
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

/**
 * KeyRequest Form
 *
 * @category Form
 * @package  Secretary
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
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
