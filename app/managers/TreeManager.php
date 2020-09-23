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
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * TreeManager constructor.
     *
     * @param PersonManager $personManager
     * @param NameManager $nameManager
     * @param WeddingManager $weddingManager
     * @param RelationManager $relationManager
     */
    public function __construct(
        PersonManager $personManager,
        WeddingManager $weddingManager,
        RelationManager $relationManager
    ) {
        $this->personManager = $personManager;
        $this->weddingManager = $weddingManager;
        $this->relationManager = $relationManager;
    }

    /**
     * @return array
     */
    public function getTree()
    {
        $persons = $this->personManager->getAll();
        $weddings = $this->weddingManager->getAll();
        $relations = $this->relationManager->getAll();

        $result = [];

        foreach ($persons as $person) {
            $row = [];
            $row['id'] = $person->id;
            $row['title'] = $person->name . ' ' . $person->surname;
            $row['parents'] = [$person->motherId, $person->fatherId];

            if ($person->gender === 'm') {
                $row['image'] = '/img/male.png';
            } else {
                $row['image'] = '/img/female.png';
            }

            foreach ($weddings as $wedding) {
                if ($person->id === $wedding->husbandId) {
                    $row['spouses'] = [$wedding->wifeId];
                } elseif ($person->id === $wedding->wifeId) {
                    $row['spouses'] = [$wedding->husbandId];
                }
            }

            foreach ($relations as $relation) {
                if ($person->id === $relation->maleId) {
                    $row['spouses'] = [$relation->femaleId];
                } elseif ($person->id === $relation->femaleId) {
                    $row['spouses'] = [$relation->maleId];
                }
            }

            $result[] = $row;
        }

        return $result;
    }
}
