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
