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
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Service;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Secretary\Entity;
use Secretary\Form;
use SecretaryCrypt\Crypt;

/**
 * Note Service
 */
class Note extends Base
{
    /**
     * @var Crypt
     */
    protected $cryptService;

    /**
     * @var \Secretary\Form\GroupSelect
     */
    protected $groupForm;

    /**
     * @var \Zend\Form\Form
     */
    protected $noteForm;


    /**
     * @var \Secretary\Form\KeyRequest
     */
    protected $keyRequestForm;

    /**
     * @param Crypt $cryptService
     * @return $this
     */
    public function setCryptService(Crypt $cryptService)
    {
        $this->cryptService = $cryptService;

        return $this;
    }

    /**
     * @return Crypt
     */
    public function getCryptService()
    {
        return $this->cryptService;
    }

    /**
     * @param Form\Group $groupForm
     * @return $this
     */
    public function setGroupForm(Form\Group $groupForm)
    {
        $this->groupForm = $groupForm;

        return $this;
    }

    /**
     * @param int $userId
     * @param string $url
     * @return \Zend\Form\Form
     */
    public function getGroupForm($userId, $url = '')
    {
        if (is_null($this->groupForm)) {
            $this->groupForm = new Form\GroupSelect($userId, $url);
            $this->groupForm->setObjectManager($this->getEntityManager());
            $this->groupForm->init();
        }

        return $this->groupForm;
    }

    /**
     * @param NoteForm $noteForm
     * @return $this
     */
    public function setNoteForm(NoteForm $noteForm)
    {
        $this->noteForm = $noteForm;

        return $this;
    }

    /**
     * @param Entity\Note $note
     * @param string $url
     * @param string $action
     * @param array $members
     * @return \Zend\Form\Form
     */
    public function getNoteForm(Entity\Note $note, $url = '', $action = 'add', $members = null)
    {
        if (is_null($this->noteForm)) {
            $builder        = new AnnotationBuilder($this->getEntityManager());
            $this->noteForm = $builder->createForm($note);
            $this->noteForm->setAttribute('action', $url);
            $this->noteForm->setAttribute('id', 'noteForm');
            $this->noteForm->setHydrator(new DoctrineObject(
                $this->getEntityManager(),
                'Secretary\Entity\Note'
            ));
            $this->noteForm->bind($note);
            if ($action == 'edit' && $note->getPrivate() === false) {
                $this->noteForm->remove('private');
                $group         = $note->getGroup();
                $membersString = $this->getMembersString(array_keys($members));
                $this->noteForm->get('group')->setValue($group->getId());
                $this->noteForm->get('members')->setValue($membersString);
            } else {
                $this->noteForm->get('private')->setAttribute('required', false);
                $this->noteForm->getInputFilter()->get('private')->setRequired(false);
            }
        }

        return $this->noteForm;
    }

    /**
     * @param Form\KeyRequest $keyRequestForm
     * @return $this
     */
    public function setKeyRequestForm(Form\KeyRequest $keyRequestForm)
    {
        $this->keyRequestForm = $keyRequestForm;

        return $this;
    }

    /**
     * @param string $url
     * @return Form\KeyRequest
     */
    public function getKeyRequestForm($url = '')
    {
        if (is_null($this->keyRequestForm)) {
            $this->keyRequestForm = new Form\KeyRequest($url);
        }

        return $this->keyRequestForm;
    }

    /**
     * @param int $userId
     * @param int $noteId
     * @return bool
     */
    public function checkNoteEditPermission($userId, $noteId)
    {
        /* @var $user2noteRecord Entity\User2Note */
        $user2noteRecord = $this->getUser2NoteRepository()->fetchUserNote($userId, $noteId);
        if (empty($user2noteRecord)) {
            return false;
        }

        if (true === $user2noteRecord->getOwner() ||
            true === $user2noteRecord->getWritePermission())
        {
            return true;
        }

        return false;
    }

    /**
     * @param  int $userId
     * @param  int $noteId
     * @return bool
     */
    public function checkNoteViewPermission($userId, $noteId)
    {
        /* @var $user2noteRecord Entity\User2Note */
        $user2noteRecord = $this->getUser2NoteRepository()->fetchUserNote($userId, $noteId);
        if (empty($user2noteRecord)) {
            return false;
        }

        if (true === $user2noteRecord->getOwner() ||
            true === $user2noteRecord->getReadPermission())
        {
            return true;
        }

        return false;
    }

    /**
     * @param int $userId
     * @param int $noteId
     * @return void
     */
    public function deleteUserNote($userId, $noteId)
    {
        $note = $this->fetchNote($noteId);
        $user2Note = $this->getUser2NoteRepository()->findOneBy(
            array('userId' => $userId, 'noteId' => $noteId)
        );
        $this->em->remove($user2Note);
        $this->em->remove($note);
        $this->em->flush();

        return;
    }

    /**
     * @param  int    $noteId
     * @param  int    $userId
     * @param  string $keyCert
     * @param  string $passphrase
     * @return array  With 'note' and 'decrypted' keys
     * @throws \LogicException If key is not readable
     * @throws \LogicException If note could note be decrypted
     */
    public function doNoteEncryption($noteId, $userId, $keyCert, $passphrase)
    {
        $validationCheck = $this->validateKey($keyCert, $passphrase);
        // Show key read error
        if (false === $validationCheck) {
            throw new \LogicException('Your key is not readable');
        }
        // Fetch Note
        $note      = $this->fetchNoteWithUserData($noteId, $userId);
        $decrypted = $this->decryptNote(
            $note['content'],
            $note['eKey'],
            $keyCert,
            $passphrase
        );
        // Show key read error
        if (false === $decrypted) {
            throw new \LogicException('Note could not be decrypted');
        }

        return array(
            'note'      => $note,
            'decrypted' => $decrypted
         );
    }

    /**
     * @param  int $id
     * @return \Secretary\Entity\Note
     */
    public function fetchNote($id)
    {
        return $this->getNoteRepository()->fetchNote($id);
    }

    /**
     * @param  int $noteId
     * @param  int $userId
     * @return \Secretary\Entity\Note
     */
    public function fetchNoteWithUserData($noteId, $userId)
    {
        return $this->getNoteRepository()->fetchNoteWithUserData($noteId, $userId);
    }

    /**
     * @param  int $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function fetchUserNotes($userId)
    {
        $notesQb = $this->getNoteRepository()->fetchUserNotes($userId);
        $notesQb->addOrderBy('n.title', 'ASC');

        return $notesQb->getQuery()->getArrayResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Entity\User $user
     * @return QueryBuilder
     */
    public function createUserNotesJoinQuery(QueryBuilder $queryBuilder, Entity\User $user)
    {
        return $queryBuilder->addSelect(array('u2n.owner', 'u2n.readPermission', 'u2n.writePermission'))
            ->leftJoin('row.user2note', 'u2n')
            ->leftJoin('u2n.user', 'u')
            ->where('u2n.userId = :userId')
            ->andWhere('u.id = :userId')
            ->andWhere('row.private = :private')
            ->setParameter('userId', $user->getId())
            ->setParameter('private', 1);
    }

    /**
     * @param  int $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function fetchUserNotesDashboard($userId)
    {
        $notesQb = $this->getNoteRepository()->fetchUserNotes($userId);
        $notesQb->addOrderBy('n.dateUpdated', 'DESC')
            ->setMaxResults(5);

        return $notesQb->getQuery()->getArrayResult();
    }

    /**
     * @param int $userId
     * @param int $groupId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function fetchGroupNotes($userId, $groupId = null)
    {
        $notesQb = $this->getNoteRepository()->fetchGroupNotes($userId, $groupId);
        $notesQb->addOrderBy('n.title', 'ASC');

        return $notesQb->getQuery()->getArrayResult();
    }

    /**
     * @param int $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function fetchGroupNotesDashboard($userId)
    {
        $notesQb = $this->getNoteRepository()->fetchGroupNotes($userId);
        $notesQb->addOrderBy('n.dateUpdated', 'DESC')
            ->setMaxResults(5);

        return $notesQb->getQuery()->getArrayResult();
    }

    /**
     * @param Entity\User $user
     * @param Entity\Group $group
     * @return void
     */
    public function deleteUserFromGroupNotes(Entity\User $user, Entity\Group $group)
    {
        $groupNotesQb = $this->getNoteRepository()->fetchGroupNotes(
            $user->getId(), $group->getId()
        );
        $groupNotesQb->addOrderBy('n.title', 'ASC');
        $groupNotes = $groupNotesQb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_OBJECT);

        if (empty($groupNotes)) {
            return;
        }

        $groupOwner = $this->getUserRepository()->find($group->getOwner());
        foreach ($groupNotes as $note) {
            $this->getUser2NoteRepository()->checkNoteOwnershipForLeavingUser(
                $note, $user->getId(), $groupOwner->getId()
            );
        }

        return;
    }

    /**
     * Save user note
     *
     * @param Entity\User $owner
     * @param Entity\Note $note
     * @param int $groupId
     * @param string $members
     * @return Entity\Note
     */
    public function saveGroupNote(Entity\User $owner, Entity\Note $note, $groupId, $members)
    {
        $members   = $this->getMembersArray($members);
        $usersKeys = $this->getUsersWithKeys($members, $owner, $groupId);
        $users     = $usersKeys['users'];
        $keys      = $usersKeys['keys'];

        $encryptData = $this->getCryptService()->encryptForMultipleKeys(
            $note->getContent(),
            $keys
        );

        $note->setContent($encryptData['content']);
        $this->em->persist($note);
        $this->em->flush();

        $note = $this->saveUser2NoteRelations($users, $note, $owner, $encryptData);

        $this->events->trigger('sendMail', 'note-add', array(
            'note'  => $note,
            'owner' => $owner,
            'users' => $users
        ));

        return $note;
    }

    /**
     * Save user note
     *
     * @param Entity\User $owner
     * @param Entity\Note $note
     * @param int $groupId
     * @param string $members
     * @return Entity\Note
     */
    public function updateGroupNote(Entity\User $owner, Entity\Note $note, $groupId, $members)
    {
        $members   = $this->getMembersArray($members);
        $usersKeys = $this->getUsersWithKeys($members, $owner, $groupId);
        $users     = $usersKeys['users'];
        $keys      = $usersKeys['keys'];

        $encryptData = $this->getCryptService()->encryptForMultipleKeys(
            $note->getContent(),
            $keys
        );

        // Remove Associations
        $this->getUser2NoteRepository()->removeUsersFromNote($note->getId());

        // Save Note
        $note->setContent($encryptData['content']);
        $this->em->persist($note);
        $this->em->flush();

        $note = $this->saveUser2NoteRelations($users, $note, $owner, $encryptData);

        $this->events->trigger('sendMail', 'note-edit', array(
            'note'  => $note,
            'owner' => $owner,
            'users' => $users
        ));

        return $note;
    }

    /**
     * Save user note
     *
     * @param Entity\User $user
     * @param Entity\Note $note
     * @return Entity\Note
     */
    public function saveUserNote(Entity\User $user, Entity\Note $note)
    {
        $encryptData = $this->getCryptService()->encryptForSingleKey(
            $note->getContent(),
            $user->getKey()->getPubKey()
        );
        $note->setContent($encryptData['content']);

        $this->em->persist($note);
        $this->em->flush();

        return $this->saveUser2NoteRelation($user, $note, $encryptData['ekey']);
    }

    /**
     * Save user2note relation
     *
     * @param Entity\User $user
     * @param Entity\Note $note
     * @param string $eKey
     * @return Entity\Note
     */
    public function saveUser2NoteRelation(Entity\User $user, Entity\Note $note, $eKey)
    {
        $user2Note = new Entity\User2Note();
        $user2Note->setUser($user)
            ->setUserId($user->getId())
            ->setNote($note)
            ->setNoteId($note->getId())
            ->setEkey($eKey)
            ->setOwner(true)
            ->setReadPermission(true)
            ->setWritePermission(true);

        $note->addUser2Note($user2Note);

        $this->em->persist($note);
        $this->em->flush();

        return $note;
    }

    /**
     * @param Entity\User $user
     * @param Entity\Note $note
     * @return Entity\Note
     */
    public function updateUserNote(Entity\User $user, Entity\Note $note)
    {
        $encryptData = $this->getCryptService()->encryptForSingleKey(
            $note->getContent(),
            $user->getKey()->getPubKey()
        );
        $note->setContent($encryptData['content']);

        $this->em->persist($note);
        $this->em->flush();

        /** @var Entity\User2Note $user2Note */
        $user2Note = $this->getUser2NoteRepository()->findOneBy(
            array('userId' => $user->getId(), 'noteId' => $note->getId())
        );
        $user2Note->setEkey($encryptData['ekey']);
        $note->addUser2Note($user2Note);

        $this->em->persist($note);
        $this->em->flush();

        return $note;
    }


    /**
     * @param string $contentCrypted
     * @param string $eKey
     * @param string $keyCert
     * @param string $passphrase
     * @return false/string
     */
    protected function decryptNote($contentCrypted, $eKey, $keyCert, $passphrase)
    {
        try {
            return $this->getCryptService()->decrypt(
                $contentCrypted,
                $eKey,
                $keyCert,
                $passphrase
            );
        } catch(\Exception $e) {
            //@todo logging?
        }

        return false;
    }

    /**
     * @param array $members
     * @throws \LogicException If no members are given
     * @return array
     */
    protected function getMembersString(array $members)
    {
        if (empty($members)) {
            throw new \LogicException('No members given');
        }
        $membersString  = implode(',', $members);
        $membersString .= ',';

        return $membersString;
    }

    /**
     * @param  string $members
     * @return array
     * @throws \LogicException If no members are given
     */
    protected function getMembersArray($members)
    {
        if (empty($members)) {
            throw new \LogicException('No members given');
        }
        $membersArray = explode(',', trim($members, ','));
        $membersArray = array_unique($membersArray);
        if (empty($membersArray)) {
            throw new \LogicException('You must provide at least one note member');
        }

        return $membersArray;
    }

    /**
     * @param array $members
     * @param Entity\User $owner
     * @param int $groupId
     * @return array $members
     * @throws \LogicException If given User(Member) ID does not
     * @throws \LogicException If given User(Member) has not set key
     * @throws \LogicException If given User(Member) is not member of given group
     */
    protected function getUsersWithKeys(array $members, Entity\User $owner, $groupId)
    {
        $users = array();
        $keys  = array();
        $group = $this->getGroupRepository()->find((int) $groupId);
        foreach ($members as $member) {
            /* @var $user \Secretary\Entity\User */
            $user = $this->getUserRepository()->find((int) $member);
            if (false === $user->getGroups()->contains($group)) {
                $this->events->trigger('logViolation', __METHOD__ . '::l42', array(
                    'message' => sprintf('User: %s wants to add user: %s to group: %s',
                        $owner->getEmail(),
                        $user->getEmail(),
                        $group->getName()
                    )
                ));
                throw new \LogicException('User does not belong to selected group');
            }
            if (empty($user)) {
                throw new \LogicException('User does not exists: ' . $member);
            }
            $key = $user->getKey();
            if (empty($key)) {
                throw new \LogicException('User key does not exists: ' . $member);
            }
            $users[$user->getId()] = $user;
            $keys[$user->getId()]  = $key->getPubKey();
        }

        $users[$owner->getId()] = $owner;
        $keys[$owner->getId()]  = $owner->getKey()->getPubKey();

        return array(
            'users' => $users,
            'keys'  => $keys
        );
    }

    /**
     * @param array $users
     * @param Entity\Note $note
     * @param Entity\User $owner
     * @param array $encryptData
     * @return Entity\Note
     */
    protected function saveUser2NoteRelations(
        array $users,
        Entity\Note $note,
        Entity\User $owner,
        array $encryptData
    ) {
        $i = 0;
        // Save User2Note entries
        /** @var Entity\User $user */
        foreach ($users as $user) {
            $ownerCheck = false;
            if ($owner->getId() == $user->getId()) {
                $ownerCheck = true;
            }
            $user2Note = new Entity\User2Note();
            $user2Note->setUser($user)
                ->setUserId($user->getId())
                ->setNote($note)
                ->setNoteId($note->getId())
                ->setEkey($encryptData['ekeys'][$i])
                ->setOwner($ownerCheck)
                ->setReadPermission(true)
                ->setWritePermission($ownerCheck);

            $note->addUser2Note($user2Note);

            $this->em->persist($note);
            $i++;
        }

        $this->em->flush();

        return $note;
    }

    /**
     * @param string $keyCert
     * @param string $passphrase
     * @return bool
     */
    protected function validateKey($keyCert, $passphrase)
    {
        try {
            $this->getCryptService()->validateKey($keyCert, $passphrase);
            return true;
        } catch(\Exception $e) {
            //@todo logging?
        }

        return false;
    }

    /**
     * @return Entity\Repository\Group
     */
    protected function getGroupRepository()
    {
        return $this->em->getRepository('Secretary\Entity\Group');
    }

    /**
     * @return Entity\Repository\Note
     */
    protected function getNoteRepository()
    {
        return $this->em->getRepository('Secretary\Entity\Note');
    }

    /**
     * @return Entity\Repository\User
     */
    protected function getUserRepository()
    {
        return $this->em->getRepository('Secretary\Entity\User');
    }

    /**
     * @return Entity\Repository\User2Note
     */
    protected function getUser2NoteRepository()
    {
        return $this->em->getRepository('Secretary\Entity\User2Note');
    }

}
