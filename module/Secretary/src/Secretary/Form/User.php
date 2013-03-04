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
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

/**
 * User Form
 *
 * @category Form
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class User extends Form
{
    /**
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * @param \Secretary\Entity\User $user
     * @param string                 $action
     */
    public function __construct(\Secretary\Entity\User $user, $action = '#')
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
