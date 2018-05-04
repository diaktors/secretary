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
 * @category Factory
 * @package  Secretary
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Service\Factory;

use Secretary\Service\Logger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * LoggerFactory
 *
 * @category Factory
 * @package  Secretary
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class LoggerFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $sl
     * @return \Secretary\Service\Logger
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $config      = $sl->get('config');
        $writerClass = 'Zend\Log\Writer\\' . $config['logger']['writer'];
        $writer      = new $writerClass($config['logger']['writerOptions']);
        $logger      = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        return new Logger($logger);
    }
}