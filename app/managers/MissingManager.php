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
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * MissingManager constructor.
     *
     * @param PeopleManager $personManager
     */
    public function __construct(
        PeopleManager $personManager
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
            if ($person->gender === 'm') {
                $children = $this->personManager->getByFatherId($person->id);
            } else {
                $children = $this->personManager->getByMotherId($person->id);
            }

            $person->hasChildren = count($children) !== 0;
        }

        return $personsMissing;
    }

    public function getPersonsByMissingRelations()
    {
        $personsMissing = $this->personManager->getMissingRelations();

        foreach ($personsMissing as $person) {
            if ($person->gender === 'm') {
                $children = $this->personManager->getByFatherId($person->id);
            } else {
                $children = $this->personManager->getByMotherId($person->id);
            }

            $person->hasChildren = count($children) !== 0;
        }

        return $personsMissing;
    }
}
