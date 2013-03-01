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

use \Doctrine\Common\Collections\ArrayCollection;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Secretery\Entity\Note as NoteEntity;
use Secretery\Entity\User as UserEntity;
use Secretery\Entity\Group as GroupEntity;
use Secretery\Entity\User2Note as User2NoteEntity;
use Secretery\Form\GroupSelect as GroupSelectForm;
use Secretery\Form\KeyRequest as KeyRequestForm;

/**
 * Note Mapper
 *
 * @category Mapper
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Note extends Base
{
    /**
     * @var Encryption
     */
    protected $encryptionService;

    /**
     * @var \Secretery\Form\GroupSelect
     */
    protected $groupForm;

    /**
     * @var \Zend\Form\Form
     */
    protected $noteForm;


    /**
     * @var \Secretery\Form\KeyRequest
     */
    protected $keyRequestForm;

    /**
     * @param Encryption $encryptionService
     */
    public function setEncryptionService(Encryption $encryptionService)
    {
        $this->encryptionService = $encryptionService;
        return $this;
    }

    /**
     * @return Encryption
     */
    public function getEncryptionService()
    {
        return $this->encryptionService;
    }

    /**
     * @param GroupForm $groupForm
     */
    public function setGroupForm(GroupForm $groupForm)
    {
        $this->groupForm = $groupForm;
        return $this;
    }

    /**
     * @param  int    $userId
     * @param  string $url
     * @return \Zend\Form\Form
     */
    public function getGroupForm($userId, $url = '')
    {
        if (is_null($this->groupForm)) {
            $this->groupForm = new GroupSelectForm($userId, $url);
            $this->groupForm->setObjectManager($this->getEntityManager());
            $this->groupForm->init();
        }
        return $this->groupForm;
    }

    /**
     * @param NoteForm $noteForm
     */
    public function setNoteForm(NoteForm $noteForm)
    {
        $this->noteForm = $noteForm;
        return $this;
    }

    /**
     * @param  \Secretery\Entity\Note $noteRecord
     * @param  string                 $url
     * @param  string                 $action
     * @param  array                  $members
     * @return \Zend\Form\Form
     */
    public function getNoteForm(NoteEntity $note, $url = '', $action = 'add', $members = null)
    {
        if (is_null($this->noteForm)) {
            $builder        = new AnnotationBuilder($this->getEntityManager());
            $this->noteForm = $builder->createForm($note);
            $this->noteForm->setAttribute('action', $url);
            $this->noteForm->setAttribute('id', 'noteForm');
            $this->noteForm->setHydrator(new DoctrineObject(
                $this->getEntityManager(),
                'Secretery\Entity\Note'
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
     * @param KeyRequestForm $keyRequestForm
     */
    public function setKeyRequestForm(KeyRequestForm $keyRequestForm)
    {
        $this->keyRequestForm = $keyRequestForm;
        return $this;
    }

    /**
     * @param  string $url
     * @return KeyRequestForm
     */
    public function getKeyRequestForm($url = '')
    {
        if (is_null($this->keyRequestForm)) {
            $this->keyRequestForm = new KeyRequestForm($url);
        }
        return $this->keyRequestForm;
    }

    /**
     * @param  int $userId
     * @param  int $noteId
     * @return bool
     */
    public function checkNoteEditPermission($userId, $noteId)
    {
        /* @var $user2noteRecord User2NoteEntity */
        $user2noteRecord = $this->em->getRepository('Secretery\Entity\User2Note')
            ->fetchUserNote($userId, $noteId);
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
        /* @var $user2noteRecord User2NoteEntity */
        $user2noteRecord = $this->em->getRepository('Secretery\Entity\User2Note')
            ->fetchUserNote($userId, $noteId);
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
     * @param  \Secretery\Entity\User $user
     * @param  \Secretery\Entity\Note $note
     * @return void
     */
    public function deleteUserNote($userId, $noteId)
    {
        $note = $this->fetchNote($noteId);
        $user2Note = $this->em->getRepository('Secretery\Entity\User2Note')->findOneBy(
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
     * @return \Secretery\Entity\Note
     */
    public function fetchNote($id)
    {
        return $this->em->getRepository('Secretery\Entity\Note')
            ->fetchNote($id);
    }

    /**
     * @param  int $noteId
     * @param  int $userId
     * @return \Secretery\Entity\Note
     */
    public function fetchNoteWithUserData($noteId, $userId)
    {
        return $this->em->getRepository('Secretery\Entity\Note')
            ->fetchNoteWithUserData($noteId, $userId);
    }

    /**
     * @param  int $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function fetchUserNotes($userId)
    {
        return $this->em->getRepository('Secretery\Entity\Note')
            ->fetchUserNotes($userId);
    }

    /**
     * @param  int $userId
     * @param  int $groupId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function fetchGroupNotes($userId, $groupId = null)
    {
        return $this->em->getRepository('Secretery\Entity\Note')
            ->fetchGroupNotes($userId, $groupId);
    }

    /**
     * @param  \Secretery\Entity\User  $user
     * @param  \Secretery\Entity\Group $group
     * @return void
     */
    public function deleteUserFromGroupNotes(UserEntity $user, GroupEntity $group)
    {
        $groupNotes = $this->em->getRepository('Secretery\Entity\Note')->fetchGroupNotes(
            $user->getId(), $group->getId(), \Doctrine\ORM\AbstractQuery::HYDRATE_OBJECT
        );
        if (empty($groupNotes)) {
            return;
        }

        $groupOwner = $this->em->getRepository('Secretery\Entity\User')->find($group->getOwner());
        foreach ($groupNotes as $note) {
            $this->em->getRepository('Secretery\Entity\User2Note')
                ->checkNoteOwnershipForLeavingUser(
                    $note, $user->getId(), $groupOwner->getId()
                );
        }

        return;
    }

    /**
     * Save user note
     *
     * @param  \Secretery\Entity\User $owner
     * @param  \Secretery\Entity\Note $note
     * @param  int                    $groupId
     * @param  string                 $members
     * @return \Secretery\Entity\Note
     */
    public function saveGroupNote(UserEntity $owner, NoteEntity $note, $groupId, $members)
    {
        $members   = $this->getMembersArray($members);
        $usersKeys = $this->getUsersWithKeys($members, $owner, $groupId);
        $users     = $usersKeys['users'];
        $keys      = $usersKeys['keys'];

        $encryptData = $this->getEncryptionService()->encryptForMultipleKeys(
            $note->getContent(),
            $keys
        );

        $note->setContent($encryptData['content']);
        $this->em->persist($note);
        $this->em->flush();

        return $this->saveUser2NoteRelations($users, $note, $owner, $encryptData);
    }

    /**
     * Save user note
     *
     * @param  \Secretery\Entity\User $owner
     * @param  \Secretery\Entity\Note $note
     * @param  int                    $groupId
     * @param  string                 $members
     * @return \Secretery\Entity\Note
     */
    public function updateGroupNote(UserEntity $owner, NoteEntity $note, $groupId, $members)
    {
        $members   = $this->getMembersArray($members);
        $usersKeys = $this->getUsersWithKeys($members, $owner, $groupId);
        $users     = $usersKeys['users'];
        $keys      = $usersKeys['keys'];

        $encryptData = $this->getEncryptionService()->encryptForMultipleKeys(
            $note->getContent(),
            $keys
        );

        // Remove Associations
        $this->em->getRepository('Secretery\Entity\User2Note')
            ->removeUsersFromNote($note->getId());

        // Save Note
        $note->setContent($encryptData['content']);
        $this->em->persist($note);
        $this->em->flush();

        return $this->saveUser2NoteRelations($users, $note, $owner, $encryptData);
    }

    /**
     * Save user note
     *
     * @param  \Secretery\Entity\User $user
     * @param  \Secretery\Entity\Note $note
     * @return \Secretery\Entity\Note
     */
    public function saveUserNote(UserEntity $user, NoteEntity $note)
    {
        $encryptData = $this->getEncryptionService()->encryptForSingleKey(
            $note->getContent(),
            $user->getKey()->getPubKey()
        );
        $note->setContent($encryptData['content']);

        $this->em->persist($note);
        $this->em->flush();

        $user2Note = new User2NoteEntity();
        $user2Note->setUser($user)
            ->setUserId($user->getId())
            ->setNote($note)
            ->setNoteId($note->getId())
            ->setEkey($encryptData['ekey'])
            ->setOwner(true)
            ->setReadPermission(true)
            ->setWritePermission(true);

        $note->addUser2Note($user2Note);

        $this->em->persist($note);
        $this->em->flush();
        return $note;
    }

    /**
     * @param  \Secretery\Entity\User $user
     * @param  \Secretery\Entity\Note $note
     * @return \Secretery\Entity\Note
     */
    public function updateUserNote(UserEntity $user, NoteEntity $note)
    {
        $encryptData = $this->getEncryptionService()->encryptForSingleKey(
            $note->getContent(),
            $user->getKey()->getPubKey()
        );
        $note->setContent($encryptData['content']);

        $this->em->persist($note);
        $this->em->flush();

        $user2Note = $this->em->getRepository('Secretery\Entity\User2Note')->findOneBy(
            array('userId' => $user->getId(), 'noteId' => $note->getId())
        );
        $user2Note->setEkey($encryptData['ekey']);
        $note->addUser2Note($user2Note);

        $this->em->persist($note);
        $this->em->flush();
        return $note;
    }


    /**
     * @param  string $contentCrypted
     * @param  string $eKey
     * @param  string $keyCert
     * @param  string $passphrase
     * @return false/string
     */
    protected function decryptNote($contentCrypted, $eKey, $keyCert, $passphrase)
    {
        try {
            return $this->getEncryptionService()->decrypt(
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
     * @param  string $members
     * @return array
     * @throws \LogicException If no members are given
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
     * @param  string     $members
     * @param  UserEntity $owner
     * @param  int        $groupId
     * @return array $members
     * @throws \LogicException If given User(Member) ID does not
     * @throws \LogicException If given User(Member) has not set key
     * @throws \LogicException If given User(Member) is not member of given group
     */
    protected function getUsersWithKeys($members, UserEntity $owner, $groupId)
    {
        $users = array();
        $keys  = array();
        $group = $this->em->getRepository('Secretery\Entity\Group')->find((int) $groupId);
        foreach ($members as $member) {
            /* @var $user \Secretery\Entity\User */
            $user = $this->em->getRepository('Secretery\Entity\User')->find((int) $member);
            if (false === $user->getGroups()->contains($group)) {
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
     * @param  array      $users
     * @param  NoteEntity $note
     * @param  UserEntity $owner
     * @param  array      $encryptData
     * @return Note
     */
    protected function saveUser2NoteRelations(array $users, NoteEntity $note,
                                              UserEntity $owner, array $encryptData)
    {
        $i = 0;
        // Save User2Note entries
        foreach ($users as $user) {
            $ownerCheck = false;
            if ($owner->getId() == $user->getId()) {
                $ownerCheck = true;
            }
            $user2Note = new User2NoteEntity();
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
     * @param  string $keyCert
     * @param  string $passphrase
     * @return bool
     */
    protected function validateKey($keyCert, $passphrase)
    {
        try {
            $this->getEncryptionService()->validateKey($keyCert, $passphrase);
            return true;
        } catch(\Exception $e) {
            //@todo logging?
        }
        return false;
    }

}
