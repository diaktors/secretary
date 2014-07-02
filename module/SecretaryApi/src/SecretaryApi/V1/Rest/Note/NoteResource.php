<?php
namespace SecretaryApi\V1\Rest\Note;

use Secretary\Entity;
use Secretary\Service;
use Zend\EventManager\StaticEventManager;
use Zend\Stdlib\ArrayUtils;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;

/**
 * Class NoteResource
 */
class NoteResource extends DoctrineResource
{
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @throws \InvalidArgumentException
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $data = (array) $data;

        /** @var Service\User $userService */
        $userService = $this->getServiceManager()->get('user-service');
        /** @var Service\Note $noteService */
        $noteService = $this->getServiceManager()->get('note-service');

        $user = $userService->getUserByMail($this->getIdentity()->getName());

        $note = new Entity\Note;
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($data, $note);

        if ($data['private'] == 0) {
            $groupId = $data['groupId'];
            /** @var Service\Group $groupService */
            $groupService = $this->getServiceManager()->get('group-service');
            if (empty($groupId) || !is_numeric($groupId)) {
                throw new \InvalidArgumentException('Please provide a valid "groupId" value.', 400);
            }

            $groupMemberCheck = $groupService->checkGroupMembership($groupId, $user->getId());
            if (false === $groupMemberCheck) {
                $this->events->trigger('logViolation', __METHOD__ . '::l42', array(
                    'message' => sprintf('User: %s wants to add note for GroupID: %s',
                        $user->getEmail(),
                        $groupId
                    )
                ));
                throw new \InvalidArgumentException('You are not allowed to add notes to this group.', 403);
            }

            if (empty($data['encryptData'])) {
                throw new \InvalidArgumentException('Please provide a valid "encryptData" value.', 400);
            }
            if (empty($data['users'])) {
                throw new \InvalidArgumentException('Please provide a valid "users" value.', 400);
            }

            $users = $noteService->getUsersWithKeys($data['users'], $user, $groupId);

            $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_PRE, $note);

            $this->getObjectManager()->persist($note);
            $this->getObjectManager()->flush();

            // @todo we need to add a param to not enccrypt already encrypted stuff
            $note = $noteService->saveUser2NoteRelations(
                $users['users'],
                $note,
                $user,
                $data['encryptData']
            );
        } else {
            if (empty($data['eKey'])) {
                throw new \InvalidArgumentException('Please provide a "eKey" value.', 400);
            }

            $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_PRE, $note);

            $this->getObjectManager()->persist($note);
            $this->getObjectManager()->flush();

            $note = $noteService->saveUser2NoteRelation($user, $note, $data['eKey']);
        }

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_POST, $note);

        return $note;
    }

    /**
     * Fetch a note
     *
     * @param int $noteId
     * @throws \InvalidArgumentException
     * @return ApiProblem|mixed
     */
    public function fetch($noteId)
    {
        /** @var Service\Note $noteService */
        $noteService = $this->getServiceManager()->get('note-service');
        /** @var Service\User $userService */
        $userService = $this->getServiceManager()->get('user-service');

        $user = $userService->getUserByMail($this->getIdentity()->getName());

        $noteCheck = $noteService->fetchNote($noteId);
        if ($noteCheck === null) {
            throw new \InvalidArgumentException('Note does not exists.', 404);
        }

        $data = $noteService->fetchNoteWithUserData($noteId, $user->getId());
        if (empty($data)) {
            throw new \InvalidArgumentException('You are not allowed to view this note.', 403);
        }

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_FETCH_POST, $data);

        return $data;
    }

    /**
     * Fetch all available notes for given user
     *
     * @param array $data
     * @internal param array|\Zend\Stdlib\Parameter $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($data = array())
    {
        /** @var Service\User $userService */
        $userService = $this->getServiceManager()->get('user-service');
        /** @var Service\Note $noteService */
        $noteService = $this->getServiceManager()->get('note-service');

        $user = $userService->getUserByMail($this->getIdentity()->getName());

        // Build query
        $fetchAllQuery = $this->getFetchAllQuery();
        $queryBuilder = $fetchAllQuery->createQuery($this->getEntityClass(), $data);
        if ($queryBuilder instanceof ApiProblem) {
            return $queryBuilder;
        }

        $queryBuilder = $noteService->createUserNotesJoinQuery($queryBuilder, $user);

        $adapter = $fetchAllQuery->getPaginatedQuery($queryBuilder);
        $reflection = new \ReflectionClass($this->getCollectionClass());
        $collection = $reflection->newInstance($adapter);

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_FETCH_ALL_POST, null, $collection);

        // Add event to set extra HAL data
        $entityClass = $this->getEntityClass();
        StaticEventManager::getInstance()->attach('ZF\Rest\RestController', 'getList.post',
            function ($e) use ($fetchAllQuery, $entityClass, $data) {
                /** @var \Zend\EventManager\Event $e */
                /** @var \ZF\Hal\Collection $halCollection */
                $halCollection = $e->getParam('collection');
                /** @var NoteCollection $notesCollection */
                $notesCollection = $halCollection->getCollection();
                $notesCollection->setItemCountPerPage($halCollection->getPageSize());
                $notesCollection->setCurrentPageNumber($halCollection->getPage());

                $halCollection->setAttributes(array(
                    'count' => $notesCollection->getCurrentItemCount(),
                    'total' => $notesCollection->getTotalItemCount(),
                    'collectionTotal' => $fetchAllQuery->getCollectionTotal($entityClass),
                ));

                $halCollection->setCollectionRouteOptions(array(
                    'query' => ArrayUtils::iteratorToArray($data)
                ));
        }
        );

        return $collection;
    }
}
