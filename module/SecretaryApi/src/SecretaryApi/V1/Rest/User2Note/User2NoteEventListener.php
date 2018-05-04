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
 * @package  SecretaryApi\V1\Rest\User2Note
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/wesrc/secretary
 */

namespace SecretaryApi\V1\Rest\User2Note;

use Secretary\Entity\User2Note;
use Secretary\Service;
use SecretaryApi\Event\AbstractEventListener;
use Zend\EventManager;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use ZF\ApiProblem\ApiProblem;

/**
 * Class User2NoteEventListener
 */
class User2NoteEventListener extends AbstractEventListener
{
    /**
     * @var Service\Note
     */
    protected $noteService;

    /**
     * @var Service\User
     */
    protected $userService;

    /**
     * @param Service\Note $noteService
     * @param Service\User $userService
     */
    public function __construct(Service\Note $noteService, Service\User $userService)
    {
        $this->noteService = $noteService;
        $this->userService = $userService;
    }

    /**
     * @param EventManager\EventManagerInterface $events
     */
    public function attach(EventManager\EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            DoctrineResourceEvent::EVENT_FETCH_POST,
            array($this, 'fetch')
        );
        $this->listeners[] = $events->attach(
            DoctrineResourceEvent::EVENT_PATCH_PRE,
            array($this, 'patch')
        );
    }

    /**
     * @param DoctrineResourceEvent $e
     * @return \ZF\ApiProblem\ApiProblem
     */
    public function fetch(DoctrineResourceEvent $e)
    {
        /** @var User2Note $user2note */
        $user2note = $e->getEntity();
        $user = $this->getUser($e, $this->userService);

        $viewCheck = $this->noteService->checkNoteViewPermission($user->getId(), $user2note->getNoteId());
        if ($viewCheck === false) {
            return new ApiProblem(403, 'User is not allowed to view note');
        }
    }

    /**
     * @param DoctrineResourceEvent $e
     * @return \ZF\ApiProblem\ApiProblem
     */
    public function patch(DoctrineResourceEvent $e)
    {
        /** @var User2Note $note */
        $user2note = $e->getEntity();
        $user = $this->getUser($e, $this->userService);

        $editCheck = $this->noteService->checkNoteEditPermission($user->getId(), $user2note->getNoteId());
        if ($editCheck === false) {
            return new ApiProblem(403, 'User is not allowed to edit note');
        }
    }
}
