<?php
namespace SecretaryApi\V1\Rest\Group;

use Secretary\Entity;
use Secretary\Service;
use Zend\EventManager\StaticEventManager;
use Zend\Stdlib\ArrayUtils;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;

class GroupResource extends DoctrineResource
{
    /**
     * Fetch all available group for given identity
     *
     * @param array $data
     * @internal param array|\Zend\Stdlib\Parameter $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($data = array())
    {
        /** @var Service\User $userService */
        $userService = $this->getServiceManager()->get('user-service');
        /** @var Service\Group $groupService */
        $groupService = $this->getServiceManager()->get('group-service');

        $user = $userService->getUserByMail($this->getIdentity()->getName());

        // Build query
        $fetchAllQuery = $this->getFetchAllQuery();
        $queryBuilder = $fetchAllQuery->createQuery($this->getEntityClass(), $data);
        if ($queryBuilder instanceof ApiProblem) {
            return $queryBuilder;
        }

        $queryBuilder = $groupService->fetchUserGroupsApi($queryBuilder, $user);

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
                /** @var GroupCollection $groupCollection */
                $groupCollection = $halCollection->getCollection();
                $groupCollection->setItemCountPerPage($halCollection->getPageSize());
                $groupCollection->setCurrentPageNumber($halCollection->getPage());

                $halCollection->setAttributes(array(
                    'count' => $groupCollection->getCurrentItemCount(),
                    'total' => $groupCollection->getTotalItemCount(),
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
