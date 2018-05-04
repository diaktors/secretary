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

/**
 * Key Form
 *
 * @category Form
 * @package  Secretary
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class Key extends Form
{
    /**
     * @var string
     */
    protected $mode = 'create';

    /**
     * @param string $action
     */
    public function __construct($action = '#')
    {
        parent::__construct('keyForm');
        $this->setAttribute('method', 'post')
            ->setAttribute('action', $action);
    }

    /**
     * @param string $mode
     */
    public function setMode($mode = 'create')
    {
        $this->mode = $mode;

        if ($this->mode == 'create') {
            $this->add(
                array(
                    'name' => 'passphrase',
                    'attributes' => array(
                        'type'   => 'text',
                        'required' => 'required'
                    ),
                    'options'    => array(
                        'label'  => 'Passphrase',
                    ),
                )
            );
        } elseif ($this->mode == 'add') {
            $this->add(
                array(
                    'name' => 'public_key',
                    'attributes' => array(
                        'type'   => 'textarea',
                        'required' => 'required'
                    ),
                    'options'    => array(
                        'label'  => 'Public Key',
                    ),
                )
            );
        }

        $this->addHiddenModeField();
        $this->addSubmitButton();

        return;
    }

    /**
     * @return void
     */
    protected function addSubmitButton()
    {
        $value = 'Create Key';
        if ($this->mode == 'add') {
            $value = 'Add own Key';
        }
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'   => 'submit',
                    'value'  => $value,
                    'class'  => 'btn btn-primary'
                ),
            )
        );
        return;
    }

    /**
     * @return void
     */
    protected function addHiddenModeField()
    {
        $this->add(
            array(
                'name' => 'mode',
                'attributes' => array(
                    'type'   => 'hidden',
                    'value'  => $this->mode,
                ),
            )
        );
        return;
    }
}
