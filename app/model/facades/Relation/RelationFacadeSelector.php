<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:19
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Relation;

use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\RelationEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\Relation\IRelationSelector;
use Rendix2\FamilyTree\App\Model\Managers\RelationManager;

/**
 * Class RelationFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Relation
 */
class RelationFacadeSelector extends DefaultFacadeSelector implements IRelationSelector
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
     * RelationFacade constructor.
     *
     * @param PersonManager   $personManager ,
     * @param RelationFilter  $relationFilter
     * @param RelationManager $relationManager
     */
    public function __construct(
        PersonManager $personManager,
        RelationFilter $relationFilter,
        RelationManager $relationManager
    ) {
        parent::__construct($relationFilter);

        $this->personManager = $personManager;
        $this->relationManager = $relationManager;
    }

    /**
     * @param RelationEntity[] $relations
     * @param PersonEntity[] $persons
     *
     * @return RelationEntity[]
     */
    public function join(array $relations, array $persons)
    {
        foreach ($relations as $relation) {
            foreach ($persons as $person) {
                if ($relation->_femaleId === $person->id) {
                    $relation->female = $person;
                    break;
                }
            }

            foreach ($persons as $person) {
                if ($relation->_maleId === $person->id) {
                    $relation->male = $person;
                    break;
                }
            }

            $duration = new DurationEntity((array) $relation);
            $relation->duration = $duration;
            $relation->clean();
        }

        return $relations;
    }

    public function getByMaleId($maleId)
    {
        $relations = $this->relationManager->select()->getManager()->getByMaleId($maleId);
        $persons = $this->personManager->select()->getCachedManager()->getAll();

        return $this->join($relations, $persons);
    }

    public function getByFemaleId($femaleId)
    {
        $relations = $this->relationManager->select()->getManager()->getByFemaleId($femaleId);
        $persons = $this->personManager->select()->getCachedManager()->getAll();

        return $this->join($relations, $persons);
    }

    public function getByMaleIdAndFemaleId($maleId, $femaleId)
    {
        $relations = $this->relationManager->select()->getManager()->getByMaleIdAndFemaleId($maleId, $femaleId);
        $persons = $this->personManager->select()->getCachedManager()->getAll();

        return $this->join($relations, $persons);
    }

    public function getByPrimaryKey($id)
    {
        $relation = $this->relationManager->select()->getManager()->getByPrimaryKey($id);

        $persons = $this->personManager->select()->getManager()->getByPrimaryKeys(
            [
                $relation->_femaleId,
                $relation->_maleId
            ]
        );

        return $this->join([$relation], $persons)[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        throw new NotImplementedException();
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getAll()
    {
        $relations = $this->relationManager->select()->getCachedManager()->getAll();

        $firstPartnerIds = $this->relationManager->select()->getManager()->getColumnFluent('maleId');
        $secondPartnerIds = $this->relationManager->select()->getManager()->getColumnFluent('femaleId');

        $firstPartnerPersons = $this->personManager->select()->getManager()->getBySubQuery($firstPartnerIds);
        $secondPartnerPersons = $this->personManager->select()->getManager()->getBySubQuery($secondPartnerIds);

        $persons = array_merge($firstPartnerPersons, $secondPartnerPersons);

        return $this->join($relations, $persons);
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }
}