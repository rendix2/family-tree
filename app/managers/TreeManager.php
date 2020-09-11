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
        $persons = $this->personManager->getAll();
        $weddings = $this->weddingManager->getAll();

        $result = [];

        foreach ($persons as $person) {
            $row = [];
            $row['id'] = $person->id;
            $row['title'] = $person->name . ' ' . $person->surname;
            $row['parents'] = [$person->motherId, $person->fatherId];

            foreach ($weddings as $wedding) {
                if ($person->id === $wedding->husbandId) {
                    $row['spouses'] = [$wedding->husbandId];
                } elseif ($person->id === $wedding->wifeId) {
                    $row['spouses'] = [$wedding->wifeId];
                }
            }

            $result[] = $row;
        }

        return $result;
    }
}
