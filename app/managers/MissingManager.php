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

use Dibi\Row;

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
     * @return Row[]
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
     * @return Row[]
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
     * @return Row[]
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
            ->fetchAll();
    }

    /**
     * @return Row[]
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
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingFathers()
    {
        return $this->personManager
            ->getAllFluent()
            ->where('[fatherId] IS NULL')
            ->where('[motherId] IS NOT NULL')
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingMothers()
    {
        return $this->personManager
            ->getAllFluent()
            ->where('[motherId] IS NULL')
            ->where('[fatherId] IS NOT NULL')
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingParents()
    {
        return $this->personManager
            ->getAllFluent()
            ->where('[motherId] IS NULL')
            ->where('[fatherId] IS NULL')
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingBirths()
    {
        return $this->personManager
            ->getAllFluent()
            ->where('[birthDate] IS NULL')
            ->where('[birthYear] IS NULL')
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingDeaths()
    {
        return $this->personManager
            ->getAllFluent()
            ->where('[deathDate] IS NULL')
            ->where('[deathYear] IS NULL')
            ->where('[stillAlive] = %i', 0)
            ->fetchAll();
    }

    /**
     * @return Row[]
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
            ->fetchAll();
    }
}
