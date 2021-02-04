<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:45
 */

namespace Rendix2\FamilyTree\App\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\RelationEntity;

/**
 * Class RelationFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class RelationFacade
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * RelationFacade constructor.
     *
     * @param IStorage $storage
     * @param PersonManager $personManager
     * @param RelationManager $relationManager
     */
    public function __construct(
        IStorage $storage,
        PersonManager $personManager,
        RelationManager $relationManager
    ) {
        $this->cache = new Cache($storage, self::class);
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

    /**
     * @return RelationEntity[]
     */
    public function getAll()
    {
        $relations = $this->relationManager->getAll();

        $firstPartnerIds = $this->relationManager->getColumnFluent('maleId');
        $secondPartnerIds = $this->relationManager->getColumnFluent('femaleId');

        $firstPartnerPersons = $this->personManager->getBySubQuery($firstPartnerIds);
        $secondPartnerPersons = $this->personManager->getBySubQuery($secondPartnerIds);

        $persons = array_merge($firstPartnerPersons, $secondPartnerPersons);

        return $this->join($relations, $persons);
    }

    /**
     * @return RelationEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $relationId
     *
     * @return RelationEntity
     */
    public function getByPrimaryKey($relationId)
    {
        $relation = $this->relationManager->getByPrimaryKey($relationId);

        $persons = $this->personManager->getByPrimaryKeys(
            [
                $relation->_femaleId,
                $relation->_maleId
            ]
        );

        return $this->join([$relation], $persons)[0];
    }

    /**
     * @param int $relationId
     *
     * @return RelationEntity
     */
    public function getByPrimaryKeyCached($relationId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $relationId);
    }

    /**
     * @param int $maleId
     *
     * @return RelationEntity[]
     */
    public function getByMaleId($maleId)
    {
        $relations = $this->relationManager->getByMaleId($maleId);
        $persons = $this->personManager->getAll();

        return $this->join($relations, $persons);
    }

    /**
     * @param int $maleId
     *
     * @return RelationEntity[]
     */
    public function getByMaleIdCached($maleId)
    {
        return $this->cache->call([$this, 'getByMaleId'], $maleId);
    }

    /**
     * @param int $femaleId
     *
     * @return RelationEntity[]
     */
    public function getByFemaleId($femaleId)
    {
        $relations = $this->relationManager->getByFemaleId($femaleId);
        $persons = $this->personManager->getAll();

        return $this->join($relations, $persons);
    }

    /**
     * @param int $femaleId
     *
     * @return RelationEntity[]
     */
    public function getByFemaleIdCached($femaleId)
    {
        return $this->cache->call([$this, 'getByFemaleId'], $femaleId);
    }
}
