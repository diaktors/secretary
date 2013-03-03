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
 * @category Repository
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
 */

namespace Secretery\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Repository
 *
 * @category Repository
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
 */
class User extends EntityRepository
{
    /**
     * @param  int $noteId
     * @param  int $groupId
     * @param  int $userId
     * @return array
     */
    public function fetchNoteGroupMembers($noteId, $groupId, $userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from($this->_entityName, 'u', 'u.id')
            ->select('u.id, u.displayName, u.email')
            ->join('u.groups', 'g')
            ->where('g.id = :groupId')
            ->andWhere('u.id != :userId')
            ->andWhere('EXISTS (SELECT u2n.noteId FROM Secretery\Entity\User2Note u2n WHERE u2n.userId = u.id AND u2n.noteId = :noteId)')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->addGroupBy('u.id')
            ->setParameter('groupId', $groupId)
            ->setParameter('noteId', $noteId)
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param  int $noteId
     * @param  int $groupId
     * @param  int $userId
     * @return array
     */
    public function fetchNoteGroupMembersUnselected($noteId, $groupId, $userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from($this->_entityName, 'u', 'u.id')
            ->select('u.id, u.displayName, u.email')
            ->join('u.groups', 'g')
            ->where('g.id = :groupId')
            ->andWhere('u.id != :userId')
            ->andWhere('NOT EXISTS (SELECT u2n.noteId FROM Secretery\Entity\User2Note u2n WHERE u2n.userId = u.id AND u2n.noteId = :noteId)')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->addGroupBy('u.id')
            ->setParameter('noteId', $noteId)
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getArrayResult();
    }
    /**
     * @param  int $userId
     * @param  int $groupId
     * @return ArrayCollection
     */
    public function getSelectUser($userId, $groupId = null)
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.roles', 'r')
            ->where('u.id != :userId')
            ->andWhere('r.roleId = :role')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->setParameter('userId', $userId)
            ->setParameter('role', 'keyuser');
        if (!empty($groupId)) {
            $memberIds = $this->getEntityManager()->getRepository('Secretery\Entity\Group')
                ->fetchGroupMemberIds($groupId);
            $qb->andWhere($qb->expr()->notIn('u.id', $memberIds));
        }

        return $qb->getQuery()->getResult();
    }
}
