<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace Core\Engine\Search\Lucene;

use Doctrine\Common,
    Doctrine\ORM,
    Doctrine\ORM\Event;

/**
 * Indexable Entity Listener
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class IndexableEntityListener implements Common\EventSubscriber
{
    /**
     * Search index manager
     * 
     * @var IndexManager
     */
    private $indexManager;

    /**
     * Construct
     *
     * @param IndexManagerInterface $indexManager
     */
    public function __construct(IndexManagerInterface $indexManager)
    {
        $this->setIndexManager($indexManager);
    }

    /**
     * Get the index manager
     *
     * @return IndexManager
     */
    public function getIndexManager()
    {
        return $this->indexManager;
    }

    /**
     * Set the index manager
     *
     * @param IndexManagerInterface $indexManager
     * @return IndexableEntityListener
     */
    public function setIndexManager(IndexManagerInterface $indexManager)
    {
        $this->indexManager = $indexManager;
        return $this;
    }


    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            ORM\Events::postPersist,
            ORM\Events::postUpdate,
            ORM\Events::postRemove
        );
    }

    /**
     * Post Persist
     *
     * @param Event\LifecycleEventArgs $e
     */
    public function postPersist(Event\LifecycleEventArgs $e)
    {
        $entity = $e->getEntity();

        // Only index indexable entities
        if (!$this->isIndexable($entity)) {
            return;
        }

        try {
            $indexManager = $this->getIndexManager();
            $indexManager->index($entity, false);
        } catch (\Exception $e) {
            trigger_error($e, \E_USER_WARNING);
        }
    }

    /**
     * Post Update
     *
     * @param Event\LifecycleEventArgs $e
     */
    public function postUpdate(Event\LifecycleEventArgs $e)
    {
        $entity = $e->getEntity();

        // Only index indexable entities
        if (!$this->isIndexable($entity)) {
            return;
        }

        try {
            $indexManager = $this->getIndexManager();
            $indexManager->index($entity);
        } catch (\Exception $e) {
            trigger_error($e, \E_USER_WARNING);
        }
    }

    /**
     * Post Remove
     * @param Event\LifecycleEventArgs $e
     */
    public function postRemove(Event\LifecycleEventArgs $e)
    {
        $entity = $e->getEntity();

        // Only index indexable entities
        if (!$this->isIndexable($entity)) {
            return;
        }

        try {
            $indexManager = $this->getIndexManager();
            $indexManager->delete($entity->getSearchDocumentId());
        } catch (\Exception $e) {
            trigger_error($e, \E_USER_WARNING);
        }
    }

    /**
     * Whether an entity is indexable
     *
     * @param IndexableInterface $entity
     * @return boolean
     */
    protected function isIndexable($entity)
    {
        return ($entity instanceof IndexableInterface);
    }
}