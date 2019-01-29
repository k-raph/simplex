<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26/01/2019
 * Time: 18:49
 */

namespace Simplex\DataMapper\Relations;


use Simplex\DataMapper\EntityManager;

interface RelationInterface
{

    /**
     * Loads all the relations for givens entities
     *
     * @param EntityManager $em
     * @param array $entities
     * @param array $fields
     * @return array
     */
    public function load(EntityManager $em, array $entities, array $fields): array;

    /**
     * Assign loaded relations to given sources
     *
     * @param EntityManager $manager
     * @param string $name
     * @param array $sources
     * @param array $targets
     * @return array
     */
    public function assign(EntityManager $manager, string $name, array $sources, array $targets): array;

    /**
     * Gets the target class
     *
     * @return string
     */
    public function getTarget(): string;
}