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

use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Secretery\Entity\Note as NoteEntity;
use Secretery\Entity\User as UserEntity;
use Secretery\Entity\User2Note as User2NoteEntity;
use Secretery\Form\Note as NoteForm;
use Secretery\Form\KeyRequest as KeyRequestForm;
use Secretery\Mapper\BaseMapper;

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
class Note extends BaseMapper
{
    /**
     * @var Key
     */
    protected $keyService;

    /**
     * @var NoteForm
     */
    protected $noteForm;


    /**
     * @var KeyRequestForm
     */
    protected $keyRequestForm;

    /**
     * @param Key $keyService
     */
    public function setKeyService(Key $keyService)
    {
        $this->keyService = $keyService;
        return $this;
    }

    /**
     * @return Key
     */
    public function getKeyService()
    {
        return $this->keyService;
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
     * @param KeyRequestForm $keyRequestForm
     */
    public function setKeyRequestForm(KeyRequestForm $keyRequestForm)
    {
        $this->keyRequestForm = $keyRequestForm;
        return $this;
    }

    /**
     * @param  \Secretery\Entity\Note $noteRecord
     * @param  string                 $url
     * @return \Zend\Form\Form
     */
    public function getNoteForm(NoteEntity $note, $url = '')
    {
        if (is_null($this->noteForm)) {
            $builder        = new AnnotationBuilder($this->getEntityManager());
            $this->noteForm = $builder->createForm($note);
            $this->noteForm->setAttribute('action', $url);
            $this->noteForm->setHydrator(new DoctrineObject(
                $this->getEntityManager(),
                'Secretery\Entity\Note'
            ));
            $this->noteForm->bind($note);
        }
        return $this->noteForm;
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
     * @param  string $contentCrypted
     * @param  string $eKey
     * @param  string $keyCert
     * @param  string $passphrase
     * @return false/string
     */
    public function decryptNote($contentCrypted, $eKey, $keyCert, $passphrase)
    {
        try {
            return $this->getKeyService()->decrypt(
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
     * Save user note
     *
     * @param  \Secretery\Entity\User $user
     * @param  \Secretery\Entity\Note $note
     * @return \Secretery\Entity\Note
     */
    public function saveUserNote(UserEntity $user, NoteEntity $note)
    {
        $encryptData = $this->keyService->encryptForSingleKey(
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
     * @param  string $keyCert
     * @param  string $passphrase
     * @return bool
     */
    public function validateKey($keyCert, $passphrase)
    {
        try {
            $this->getKeyService()->validateKey($keyCert, $passphrase);
            return true;
        } catch(\Exception $e) {
            //@todo logging?
        }
        return false;
    }

}
