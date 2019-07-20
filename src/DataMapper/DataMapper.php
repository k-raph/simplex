<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/05/2019
 * Time: 21:00
 */

namespace Simplex\DataMapper;

use Simplex\Database\DatabaseManager;

class DataMapper
{

    /**
     * @var DatabaseManager
     */
    private $manager;

    /**
     * @var EntityManager[]
     */
    private $managers = [];

    /**
     * DataMapper constructor.
     * @param DatabaseManager $manager
     */
    public function __construct(DatabaseManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Gets entity manager for given database name
     *
     * @param string $name
     * @return EntityManager
     */
    public function getManager(string $name = 'default'): EntityManager
    {
        if (!in_array($name, $this->managers)) {
            $manager = new EntityManager($this->manager->getDatabase($name));
            $this->managers[$name] = $manager;
        }

        return $this->managers[$name];
    }
}
