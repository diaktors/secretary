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
 * @category Service
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Service;

use Doctrine\ORM\QueryBuilder;
use Secretary\Entity;

/**
 * Group Service
 *
 * @category Service
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class Group extends Base
{
    /**
     * @param  int $userId
     * @param  int $groupId
     * @return bool
     * @throws \InvalidArgumentException If GroupID is invalid
     * @throws \InvalidArgumentException If UserID is invalid
     */
    public function checkGroupMembership($groupId, $userId)
    {
        if (empty($groupId) || !is_numeric($groupId)) {
            throw new \InvalidArgumentException('Please provide a valid GroupID');
        }
        if (empty($userId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please provide a valid UserID');
        }
        return $this->getGroupRepository()->checkGroupMembership($groupId, $userId);
    }

    /**
     * @param  int $groupId
     * @return Entity\Group
     * @throws \InvalidArgumentException If GroupID is invalid
     */
    public function fetchGroup($groupId)
    {
        if (empty($groupId) || !is_numeric($groupId)) {
            throw new \InvalidArgumentException('Please provide a valid GroupID');
        }
        return $this->getGroupRepository()->find($groupId);
    }

    /**
     * @param  int $groupId
     * @param  int $userId
     * @return array
     * @throws \InvalidArgumentException If GroupID is invalid
     */
    public function fetchGroupMembers($groupId, $userId = null)
    {
        if (empty($groupId) || !is_numeric($groupId)) {
            throw new \InvalidArgumentException('Please provide a valid GroupID');
        }
        return $this->getGroupRepository()->fetchGroupMembers($groupId, $userId);
    }

    /**
     * @param  int $noteId
     * @param  int $groupId
     * @param  int $userId
     * @return array
     * @throws \InvalidArgumentException If GroupID is invalid
     * @throws \InvalidArgumentException If NoteID is invalid
     * @throws \InvalidArgumentException If UserID is invalid
     */
    public function fetchNoteGroupMembers($noteId, $groupId, $userId)
    {
        if (empty($noteId) || !is_numeric($noteId)) {
            throw new \InvalidArgumentException('Please provide a valid NoteID');
        }
        if (empty($groupId) || !is_numeric($groupId)) {
            throw new \InvalidArgumentException('Please provide a valid GroupID');
        }
        if (empty($userId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please provide a valid UserID');
        }
        return $this->getUserRepository()->fetchNoteGroupMembers($noteId, $groupId, $userId);
    }

    /**
     * @param  int $noteId
     * @param  int $groupId
     * @param  int $userId
     * @return array
     * @throws \InvalidArgumentException If GroupID is invalid
     * @throws \InvalidArgumentException If NoteID is invalid
     * @throws \InvalidArgumentException If UserID is invalid
     */
    public function fetchNoteGroupMembersUnselected($noteId, $groupId, $userId)
    {
        if (empty($noteId) || !is_numeric($noteId)) {
            throw new \InvalidArgumentException('Please provide a valid NoteID');
        }
        if (empty($groupId) || !is_numeric($groupId)) {
            throw new \InvalidArgumentException('Please provide a valid GroupID');
        }
        if (empty($userId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please provide a valid UserID');
        }
        return $this->getUserRepository()->fetchNoteGroupMembersUnselected(
            $noteId, $groupId, $userId
        );
    }

    /**
     * @param  int $userId
     * @return array
     * @throws \InvalidArgumentException If UserID is invalid
     */
    public function fetchUserGroups($userId)
    {
        if (empty($userId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please provide a valid UserID');
        }
        return $this->getGroupRepository()->fetchUserGroups($userId);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Entity\User $user
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \InvalidArgumentException
     */
    public function fetchUserGroupsApi(QueryBuilder $queryBuilder, Entity\User $user)
    {
        return $queryBuilder->join('row.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());
    }

    /**
     * @param  Entity\User $user
     * @param  string     $groupname
     * @return Entity\Group
     */
    public function addUserGroup(Entity\User $user, $groupname)
    {
        $groupRecord = new Entity\Group();
        $groupRecord->setName($groupname)
            ->setOwner($user->getId());
        $user->addGroup($groupRecord);
        $this->em->persist($user);
        $this->em->flush();
        return $groupRecord;
    }


    /**
     * @param  Entity\Group $group
     * @param  int         $userId
     * @return Entity\User
     * @throws \InvalidArgumentException If UserID is invalid
     * @throws \LogicException           If User could not been found
     */
    public function addGroupMember(Entity\Group $group, $userId)
    {
        if (empty($userId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please provide a valid UserID');
        }
        $userRecord = $this->getUserRepository()->find($userId);
        if (empty($userRecord)) {
            throw new \LogicException('User could not been found');
        }
        $userRecord->addGroup($group);
        $this->em->persist($userRecord);
        $this->em->flush();
        return $userRecord;
    }

    /**
     * @param  Entity\User  $user
     * @param  Entity\Group $group
     * @return User
     */
    public function deleteUserGroup(Entity\User $user, Entity\Group $group)
    {
        $user->getGroups()->removeElement($group);
        $this->em->persist($user);
        $this->em->flush();
        if ($group->getUsers()->count() == 0) {
            $this->em->remove($group);
            $this->em->flush();
        }
        return $user;
    }

    /**
     * @param  Entity\User  $user
     * @param  Entity\Group $group
     * @return User
     */
    public function removeUserFromGroup(Entity\User $user, Entity\Group $group)
    {
        $user->getGroups()->removeElement($group);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @param  Entity\Group $group
     * @param  string      $groupname
     * @return Entity\Group
     */
    public function updateGroup(Entity\Group $group, $groupname)
    {
        $group->setName($groupname);
        $this->em->persist($group);
        $this->em->flush();
        return $group;
    }

    /**
     * @return \Secretary\Entity\Repository\Group
     */
    protected function getGroupRepository()
    {
        return $this->em->getRepository('Secretary\Entity\Group');
    }

    /**
     * @return \Secretary\Entity\Repository\User
     */
    protected function getUserRepository()
    {
        return $this->em->getRepository('Secretary\Entity\User');
    }

}
