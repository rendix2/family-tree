<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TreeManager.php
 * User: Tomáš Babický
 * Date: 27.08.2020
 * Time: 16:15
 */

namespace Rendix2\FamilyTree\App\Managers;

/**
 * Class TreeManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TreeManager
{
    /**
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * TreeManager constructor.
     *
     * @param PeopleManager $personManager
     * @param NameManager $nameManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        PeopleManager $personManager,
        NameManager $nameManager,
        WeddingManager $weddingManager
    ) {
        $this->personManager = $personManager;
        $this->nameManager = $nameManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return array
     */
    public function getTree()
    {
        $persons = $this->personManager->get();

        foreach ($persons as $person) {
            $lastWedding  = $this->weddingManager->getLastByWifeId($person->id);
            $namesArray = $this->nameManager->getByPersonId($person->id);
            $names = [];

            // set names
            foreach ($namesArray as $name) {
                $nameString = $name->name . ' ' . $name->surname;

                if ($name->dateSince !== null && $name->dateTo !== null) {
                    $nameString .= ' (' . date_format($name->dateSince, 'd.m.Y') . ' - ' . date_format($name->dateTo, 'd.m.Y') . ')';
                }

                if ($name->dateSince !== null && $name->dateTo === null) {
                    $nameString .= ' (' . date_format($name->dateSince, 'd.m.Y') . ' - ' . date_format(new \DateTime(),'d.m.Y') . ')';
                }

                if ($name->dateSince === null && $name->dateTo !== null) {
                    $nameString .= ' (dd.mm.yyyy - ' . date_format($name->dateTo, 'd.m.Y') . ')';
                }

                $names[] = $nameString;
            }

            $person->names = $names;

            // set partner
            if ($lastWedding) {
                $person->tags = ['partner'];
                $person->pid = $lastWedding->husbandId;
            }
            
            // set parents
            if (
                !isset($person->ppid) &&
                $person->motherId !== null &&
                $person->fatherId !== null &&
                !isset($person->pid)
            ) {
                $person->pid = $person->fatherId;
                $person->ppid = $person->motherId;
            }

            unset($person->fatherId, $person->motherId);
        }

        return $persons;
    }
}
