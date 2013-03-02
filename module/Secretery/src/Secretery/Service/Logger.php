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
 * @category Mapper
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Service;

use \Zend\Log\LoggerInterface;

/**
 * Logger Service
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Logger
{
    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    /**
     * @param \Zend\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param  string $msg
     * @return \Zend\Log\Logger
     */
    public function logError($msg)
    {
        return $this->logger->err($msg);
    }

    /**
     * @param  string $msg
     * @return \Zend\Log\Logger
     */
    public function logInfo($msg)
    {
        return $this->logger->info($msg);
    }

    /**
     * @param  string $msg
     * @return \Zend\Log\Logger
     */
    public function logViolation($msg)
    {
        return $this->logger->crit($msg);
    }

}