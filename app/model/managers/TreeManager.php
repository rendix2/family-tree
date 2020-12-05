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

use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\RelationEntity;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
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
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * TreeManager constructor.
     *
     * @param PersonFacade $personFacade
     * @param WeddingFacade $weddingFacade
     * @param RelationFacade $relationFacade
     */
    public function __construct(
        PersonFacade $personFacade,
        WeddingFacade $weddingFacade,
        RelationFacade $relationFacade
    ) {
        $this->personFacade = $personFacade;
        $this->weddingFacade = $weddingFacade;
        $this->relationFacade = $relationFacade;
    }

    /**
     * @param PersonEntity[] $persons
     * @param WeddingEntity[] $weddings
     * @param RelationEntity[] $relations
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
            $row['parents'] = [$person->mother ? $person->mother->id : null, $person->father ? $person->father->id : null];

            $row['description'] = '';

            if ($person->hasBirthDate) {
                $row['description'] .= date_format($person->birthDate, 'd.m.Y');

                if ($person->birthTown) {
                    $row['description'] .= "\n" .'(' . $person->birthTown->name . ')' ;
                }
            }

            if ($person->hasDeathDate) {
                $row['description'] .= "\n " . date_format($person->deathDate, 'd.m.Y');

                if ($person->deathTown) {
                    $row['description'] .= "\n" . '(' . $person->deathTown->name . ')';
                }
            }

            if ($person->gender === 'm') {
                $row['image'] = '/img/male.png';
            } else {
                $row['image'] = '/img/female.png';
            }

            foreach ($weddings as $wedding) {
                if ($person->id === $wedding->husband->id) {
                    $row['spouses'][] = $wedding->wife->id;
                    continue;
                } elseif ($person->id === $wedding->wife->id) {
                    $row['spouses'][] = $wedding->husband->id;
                    continue;
                }
            }

            foreach ($relations as $relation) {
                if ($person->id === $relation->male->id) {
                    $row['spouses'][] = $relation->female->id;
                    continue;
                } elseif ($person->id === $relation->female->id) {
                    $row['spouses'][] = $relation->male->id;
                    continue;
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
        $persons = $this->personFacade->getAllCached();
        $weddings = $this->weddingFacade->getAllCached();
        $relations = $this->relationFacade->getAllCached();

        return $this->iterateTree($persons, $weddings, $relations);
    }

    /**
     * @param int $genusId
     * @return array
     */
    public function getGenusTree($genusId)
    {
        $genusPersons = $this->personFacade->getByGenusIdCached($genusId);
        $allPersons = $this->personFacade->getAllCached();
        $weddings = $this->weddingFacade->getAllCached();
        $relations = $this->relationFacade->getAllCached();

        $newGenusPersons = [];

        foreach ($genusPersons as $genusPerson) {
            $newGenusPersons[] = $genusPerson;

            // adding missing persons (spouses and partners)
            foreach ($weddings as $wedding) {
                if ($genusPerson->id === $wedding->husband->id) {
                    foreach ($allPersons as $allPerson) {
                        if ($allPerson->id === $wedding->wife->id) {
                            $allPerson->mother = null;
                            $allPerson->father = null;
                            $newGenusPersons[] = $allPerson;
                            break;
                        }
                    }
                }

                if ($genusPerson->id === $wedding->wife->id) {
                    foreach ($allPersons as $allPerson) {
                        if ($allPerson->id === $wedding->husband->id) {
                            $allPerson->mother = null;
                            $allPerson->father = null;
                            $newGenusPersons[] = $allPerson;
                            break;
                        }
                    }
                }
            }

            foreach ($relations as $relation) {
                if ($genusPerson->id === $relation->male->id) {
                    foreach ($allPersons as $allPerson) {
                        if ($allPerson->id === $relation->female->id) {
                            $allPerson->mother = null;
                            $allPerson->father = null;
                            $newGenusPersons[] = $allPerson;
                            break;
                        }
                    }
                }

                if ($genusPerson->id === $relation->female->id) {
                    foreach ($allPersons as $allPerson) {
                        if ($allPerson->id === $relation->male->id) {
                            $allPerson->mother = null;
                            $allPerson->father = null;
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
        $persons = $this->personFacade->getAllCached();
        $person = $this->personFacade->getByPrimaryKeyCached($personId);
        $weddings = $this->weddingFacade->getAllCached();
        $relations = $this->relationFacade->getAllCached();

        // our js family tree lib does not care about order :(
        $dsg = $this->dsg($persons, $person);

        return $this->iterateTree($dsg, $weddings, $relations);
    }

    /**
     * @param PersonEntity[] $persons
     * @param PersonEntity $root
     *
     * @return PersonEntity[]
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
                if ($v->mother && $person->id === $v->mother->id) {
                    $s->push($person);
                    continue;
                }

                if ($v->father && $person->id === $v->father->id) {
                    $s->push($person);
                    continue;
                }
            }
        }

        return $resultPersons;
    }

    /**
     * @param PersonEntity[] $persons
     * @param PersonEntity $root
     *
     * @return PersonEntity[]
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
                    continue;
                }

                if ($person->id === $v->fatherId) {
                    $q->enqueue($person);
                    continue;
                }
            }
        }

        return $result;
    }
}
