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

use Dibi\Row;
use SplQueue;
use SplStack;

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
     * @param array $persons
     * @param array $weddings
     * @param array $relations
     *
     * @return array
     */
    private function iterateTree(array $persons, array $weddings, array $relations)
    {
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

    /**
     * @return array
     */
    public function getAllFamilyTree()
    {
        $persons = $this->personManager->getAll();
        $weddings = $this->weddingManager->getAll();
        $relations = $this->relationManager->getAll();

        return $this->iterateTree($persons, $weddings, $relations);
    }

    /**
     * @param int $genusId
     * @return array
     */
    public function getGenusTree($genusId)
    {
        $genusPersons = $this->personManager->getByGenusId($genusId);
        $allPersons = $this->personManager->getAll();
        $weddings = $this->weddingManager->getAll();
        $relations = $this->relationManager->getAll();

        $newGenusPersons = [];

        foreach ($genusPersons as $genusPerson) {
            $newGenusPersons[] = $genusPerson;

            // adding missing persons (spouses and partners)
            foreach ($weddings as $wedding) {
                if ($genusPerson->id === $wedding->husbandId) {
                    foreach ($allPersons as $allPerson) {
                        if ($allPerson->id === $wedding->wifeId) {
                            $allPerson->motherId = null;
                            $allPerson->fatherId = null;
                            $newGenusPersons[] = $allPerson;
                            break;
                        }
                    }
                }

                if ($genusPerson->id === $wedding->wifeId) {
                    foreach ($allPersons as $allPerson) {
                        if ($allPerson->id === $wedding->husbandId) {
                            $allPerson->motherId = null;
                            $allPerson->fatherId = null;
                            $newGenusPersons[] = $allPerson;
                            break;
                        }
                    }
                }
            }

            foreach ($relations as $relation) {
                if ($genusPerson->id === $relation->maleId) {
                    foreach ($allPersons as $allPerson) {
                        if ($allPerson->id === $relation->femaleId) {
                            $allPerson->motherId = null;
                            $allPerson->fatherId = null;
                            $newGenusPersons[] = $allPerson;
                            break;
                        }
                    }
                }

                if ($genusPerson->id === $relation->femaleId) {
                    foreach ($allPersons as $allPerson) {
                        if ($allPerson->id === $relation->maleId) {
                            $allPerson->motherId = null;
                            $allPerson->fatherId = null;
                            $newGenusPersons[] = $allPerson;
                            break;
                        }
                    }
                }
            }
        }

        return $this->iterateTree($newGenusPersons,  $weddings, $relations);
    }

    /**
     * @param int $personId
     * @return array
     */
    public function getPersonTree($personId)
    {
        $persons = $this->personManager->getAll();
        $person = $this->personManager->getByPrimaryKey($personId);
        $weddings = $this->weddingManager->getAll();
        $relations = $this->relationManager->getAll();

        // our js family tree lib does not care about order :(
        $dsg = $this->dsg($persons, $person);

        return $this->iterateTree($dsg, $weddings, $relations);
    }

    /**
     * @param Row[] $persons
     * @param Row $root
     * @return array
     */
    private function dsg($persons, $root)
    {
        $s = new SplStack();
        $s->push($root);

        $resultPersons = [];

        while (!$s->isEmpty()) {
            $v = $s->pop();
            $resultPersons[] = $v;

            foreach ($persons as $person) {
                if ($person->id === $v->motherId) {
                    $s->push($person);
                }

                if ($person->id === $v->fatherId) {
                    $s->push($person);
                }
            }
        }

        return $resultPersons;
    }

    /**
     * @param Row[] $persons
     * @param Row $root
     * @return array
     */
    private function bfs($persons, $root)
    {
        $q = new SplQueue();
        $q->enqueue($root);

        $result = [];

        while (!$q->isEmpty()) {
            $v = $q->dequeue();

            $result[] = $v;

            foreach ($persons as $person) {
                if ($person->id === $v->motherId) {
                    $q->enqueue($person);
                }

                if ($person->id === $v->fatherId) {
                    $q->enqueue($person);
                }
            }
        }

        return $result;
    }
}
