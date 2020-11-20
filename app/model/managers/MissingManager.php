<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Missingmanager.php
 * User: Tomáš Babický
 * Date: 21.09.2020
 * Time: 0:32
 */

namespace Rendix2\FamilyTree\App\Managers;


use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Class MissingManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class MissingManager
{
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
     * @param PersonManager $personManager
     * @param RelationManager $relationManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        PersonManager $personManager,
        RelationManager $relationManager,
        WeddingManager $weddingManager
    ) {
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
            $children = $this->personManager->getChildrenByPerson($person);

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
            $children = $this->personManager->getChildrenByPerson($person);

            $person->hasChildren = count($children) !== 0;
        }

        return $personsMissing;
    }

    /**
     * @return PersonEntity[]
     */
    public function getMissingWeddings()
    {
        return $this->personManager->getAllFluent()
            ->where('id NOT IN',

                $this->weddingManager->getDibi()->select('husbandId')
                    ->from($this->weddingManager->getTableName())
            )
            ->where('id NOT IN',

                $this->weddingManager->getDibi()->select('wifeId')
                    ->from($this->weddingManager->getTableName())
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
            ->getAllFluent()
            ->where('id NOT IN',

                $this->relationManager->getDibi()->select('maleId')
                    ->from($this->relationManager->getTableName())
            )
            ->where('id NOT IN',

                $this->relationManager->getDibi()->select('femaleId')
                    ->from($this->relationManager->getTableName())
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
