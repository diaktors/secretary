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
 * @category EventListener
 * @package  SecretaryApi\V1\Rest\Group
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/wesrc/secretary
 */

namespace SecretaryApi\V1\Rest\Group;

use Doctrine\ORM\QueryBuilder;
use Secretary\Service;
use SecretaryApi\Event\AbstractEventListener;
use Zend\EventManager;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;

/**
 * Class GroupEventListener
 */
class GroupEventListener extends AbstractEventListener
{
    /**
     * @var Service\Group
     */
    protected $groupService;

    /**
     * @var Service\User
     */
    protected $userService;

    /**
     * @param Service\Group $groupService
     * @param Service\User $userService
     */
    public function __construct(Service\Group $groupService, Service\User $userService)
    {
        $this->groupService = $groupService;
        $this->userService = $userService;
    }

    /**
     * @param EventManager\EventManagerInterface $events
     */
    public function attach(EventManager\EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE,
            array($this, 'fetchAll')
        );
    }

    /**
     * @param DoctrineResourceEvent $e
     */
    public function fetchAll(DoctrineResourceEvent $e)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $e->getQueryBuilder();
        $user = $this->getUser($e, $this->userService);
        $this->groupService->fetchUserGroupsApi($queryBuilder, $user);
    }
}
