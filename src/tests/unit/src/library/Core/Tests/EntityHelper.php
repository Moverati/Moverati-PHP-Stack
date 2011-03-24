<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  CoreVideo
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace Core\Tests;

/**
 * Entity Helper
 *
 * @author    Josh Team
 * @category  CoreVideo
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class EntityHelper
{
    /**
     * Creates an EntityManager for testing purposes.
     *
     * NOTE: The created EntityManager will have its dependant DBAL parts completely
     * mocked out using a DriverMock, ConnectionMock, etc. These mocks can then
     * be configured in the tests to simulate the DBAL behavior that is desired
     * for a particular test,
     *
     * @return Doctrine\ORM\EntityManagerMock
     *
     * TODO create test helper for this
     */
    public static function getEntityManager($conn = null, $conf = null, $eventManager = null, $withSharedMetadata = true)
    {
        $config = new \Doctrine\ORM\Configuration();
        if($withSharedMetadata) {
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        } else {
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        }
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace('Doctrine\Tests\Proxies');
        $eventManager = new \Doctrine\Common\EventManager();
        if ($conn === null) {
            $conn = array(
                'driverClass' => 'Doctrine\Tests\Mocks\DriverMock',
                'wrapperClass' => 'Doctrine\Tests\Mocks\ConnectionMock',
                'user' => 'john',
                'password' => 'wayne'
            );
        }
        if (is_array($conn)) {
            $conn = \Doctrine\DBAL\DriverManager::getConnection($conn, $config, $eventManager);
        }
        return \Doctrine\Tests\Mocks\EntityManagerMock::create($conn, $config, $eventManager);
    }
}
