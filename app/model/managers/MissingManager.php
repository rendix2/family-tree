<?php
/**
 *
 * Created by PhpStorm.
 * Filename: MissingManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:12
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Class MissingManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class MissingManager
{
    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * MissingManager constructor.
     *
     * @param Connection      $connection
     * @param PersonManager   $personManager
     * @param RelationManager $relationManager
     * @param WeddingManager  $weddingManager
     */
    public function __construct(
        Connection $connection,
        PersonManager $personManager,
        RelationManager $relationManager,
        WeddingManager $weddingManager
    ) {
        $this->connection = $connection;

        $this->personManager = $personManager;
        $this->relationManager = $relationManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return PersonEntity[]
     */
    public function getPersonsByMissingWeddings()
    {
        $personsMissing = $this->getMissingWeddings();

        foreach ($personsMissing as $person) {
            $children = $this->personManager->select()->getManager()->getChildrenByPerson($person);

            $person->hasChildren = count($children) !== 0;
        }

        return $personsMissing;
    }

    /**
     * @return PersonEntity[]
     */
    public function getPersonsByMissingRelations()
    {
        $personsMissing = $this->getMissingRelations();

        foreach ($personsMissing as $person) {
            $children = $this->personManager->select()->getManager()->getChildrenByPerson($person);

            $person->hasChildren = count($children) !== 0;
        }

        return $personsMissing;
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingWeddings()
    {
        return $this->personManager
            ->select()
            ->getManager()
            ->getAllFluent()
            ->where('id NOT IN',
                $this->connection->select('husbandId')
                    ->from($this->weddingManager->getTable()->getTableName())
            )
            ->where('id NOT IN',
                $this->connection->select('wifeId')
                    ->from($this->weddingManager->getTable()->getTableName())
            )
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingRelations()
    {
        return $this->personManager
            ->select()
            ->getManager()
            ->getAllFluent()
            ->where('id NOT IN',

                $this->connection->select('maleId')
                    ->from($this->relationManager->getTable()->getTableName())
            )
            ->where('id NOT IN',

                $this->connection->select('femaleId')
                    ->from($this->relationManager->getTable()->getTableName())
            )
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingFathers()
    {
        return $this->personManager
            ->select()
            ->getManager()
            ->getAllFluent()
            ->where('[fatherId] IS NULL')
            ->where('[motherId] IS NOT NULL')
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingMothers()
    {
        return $this->personManager
            ->select()
            ->getManager()
            ->getAllFluent()
            ->where('[motherId] IS NULL')
            ->where('[fatherId] IS NOT NULL')
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingParents()
    {
        return $this->personManager
            ->select()
            ->getManager()
            ->getAllFluent()
            ->where('[motherId] IS NULL')
            ->where('[fatherId] IS NULL')
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingBirths()
    {
        return $this->personManager
            ->select()
            ->getManager()
            ->getAllFluent()
            ->where('[birthDate] IS NULL')
            ->where('[birthYear] IS NULL')
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingDeaths()
    {
        return $this->personManager
            ->select()
            ->getManager()
            ->getAllFluent()
            ->where('[deathDate] IS NULL')
            ->where('[deathYear] IS NULL')
            ->where('[stillAlive] = %i', 0)
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingDates()
    {
        return $this->personManager
            ->select()
            ->getManager()
            ->getAllFluent()
            ->where('[birthDate] IS NULL')
            ->where('[birthYear] IS NULL')
            ->where('[deathDate] IS NULL')
            ->where('[deathYear] IS NULL')
            ->where('[stillAlive] = %i', 0)
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }
}