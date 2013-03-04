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
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Group Repository
 *
 * @category Repository
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class Group extends EntityRepository
{
    /**
     * @param  int $groupId
     * @param  int $userId
     * @return bool
     */
    public function checkGroupMembership($groupId, $userId)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('u.id')
            ->join('g.users', 'u')
            ->where('g.id = :groupId')
            ->andWhere('u.id = :userId')
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId);

        $result = $qb->getQuery()->getOneOrNullResult();
        if (empty($result)) {
            return false;
        }
        return true;
    }

    /**
     * @param  int $groupId
     * @return \Secretary\Entity\Group
     */
    public function fetchGroup($groupId)
    {
        return $this->findOneBy(array('id' => $groupId));
    }

    /**
     * @param  int $groupId
     * @param  int $userId  [Filter] Remove $userId from result
     * @return array
     */
    public function fetchGroupMembers($groupId, $userId = null)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('u.id, u.displayName, u.email')
            ->join('g.users', 'u')
            ->where('g.id = :groupId')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->setParameter('groupId', $groupId);

        if (!empty($userId)) {
            $qb->andWhere('u.id != :userId')
                ->setParameter('userId', $userId);
        }

        $result = $qb->getQuery()->getArrayResult();
        $users  = array();
        foreach ($result as $user) {
            $users[] = $user;
        }

        return $users;
    }

    /**
     * @param  int $groupId
     * @return array
     */
    public function fetchGroupMemberIds($groupId)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('u.id')
            ->join('g.users', 'u')
            ->where('g.id = :groupId')
            ->setParameter('groupId', $groupId);

        $result = $qb->getQuery()->getArrayResult();
        $ids    = array();
        foreach ($result as $id) {
            array_push($ids, $id['id']);
        }

        return $ids;
    }

    /**
     * @param  int $userId
     * @return ArrayCollection
     */
    public function fetchUserGroups($userId)
    {
        $qb = $this->createQueryBuilder('g')
            ->join('g.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->addOrderBy('g.name', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
