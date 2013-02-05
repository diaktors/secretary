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
     * @param  int $id
     * @return \Secretery\Entity\Note
     */
    public function fetchNote($id)
    {
        return $this->em->getRepository('Secretery\Entity\Note')
            ->fetchNote($id);
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
}
