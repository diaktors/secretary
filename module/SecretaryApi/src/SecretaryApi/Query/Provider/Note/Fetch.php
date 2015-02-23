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
 * @category Query\Provider
 * @package  SecretaryApi\Query\Provider\Note
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/wesrc/secretary
 */

namespace SecretaryApi\Query\Provider\Note;

use Doctrine\ORM\QueryBuilder;
use Secretary\Entity;
use ZF\Apigility\Doctrine\Server\Query\Provider\AbstractQueryProvider;
use ZF\Rest\ResourceEvent;

/**
 * Class Fetch
 */
class Fetch extends AbstractQueryProvider
{
    /**
     * @param ResourceEvent $event
     * @param string $entityClass
     * @param array  $parameters
     *
     * @return QueryBuilder
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $noteId = $event->getParam('id');

        /** @var Entity\Repository\User $userRepository */
        $userRepository = $this->getObjectManager()->getRepository('Secretary\Entity\User');
        /** @var Entity\User $user */
        $user = $userRepository->findOneBy(array('email' => $event->getIdentity()->getName()));

        /** @var Entity\Repository\Note $noteRepository */
        $noteRepository = $this->getObjectManager()->getRepository($entityClass);
        $queryBuilder = $noteRepository->fetchNoteWithUserDataQuery($noteId, $user->getId());

        return $queryBuilder;
    }
} 