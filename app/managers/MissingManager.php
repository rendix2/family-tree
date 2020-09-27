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
     * MissingManager constructor.
     *
     * @param PersonManager $personManager
     */
    public function __construct(
        PersonManager $personManager
    ) {
        $this->personManager = $personManager;
    }

    /**
     * @return Row[]
     */
    public function getPersonsByMissingWeddings()
    {
        $personsMissing = $this->personManager->getMissingWeddings();

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
        $personsMissing = $this->personManager->getMissingRelations();

        foreach ($personsMissing as $person) {
            $children = $this->personManager->getChildrenByPerson($person);

            $person->hasChildren = count($children) !== 0;
        }

        return $personsMissing;
    }
}
