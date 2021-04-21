<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacadeCachedSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:46
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person;


use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces\IPersonSelector;

/**
 * Class PersonFacadeCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person
 */
class PersonFacadeCachedSelector implements IPersonSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var PersonFacadeSelector $selector
     */
    private $selector;

    /**
     * PersonFacadeCachedSelector constructor.
     *
     * @param IStorage             $storage
     * @param PersonFacadeSelector $personFacadeSelector
     */
    public function __construct(
        IStorage $storage,
        PersonFacadeSelector $personFacadeSelector
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $personFacadeSelector;
    }

    public function __destruct()
    {
        $this->cache = null;
        $this->selector = null;
    }

    public function getByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getMalesByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getFemalesByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    public function getMalesByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    public function getFemalesByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    public function getByGenusId($genusId)
    {
        return $this->cache->call([$this->selector, 'getByGenusId'], $genusId);
    }

    public function getByBirthTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByBirthAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    public function getByDeathTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByDeathAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    public function getByGravedTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByGravedAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }

    public function getMalesPairs()
    {
        throw new NotImplementedException();
    }

    public function getFemalesPairs()
    {
        throw new NotImplementedException();
    }

    public function getBrothers($fatherId, $motherId, $personId)
    {
        throw new NotImplementedException();
    }

    public function getSisters($fatherId, $motherId, $personId)
    {
        throw new NotImplementedException();
    }

    public function getSonsByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getSonsById($id)
    {
        throw new NotImplementedException();
    }

    public function getDaughtersByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getDaughtersById($id)
    {
        throw new NotImplementedException();
    }

    public function getChildrenByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getChildrenById($id)
    {
        throw new NotImplementedException();
    }

    public function calculateAgeById($id)
    {
        throw new NotImplementedException();
    }

    public function calculateAgeByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getByPrimaryKey($id)
    {
        return $this->cache->call([$this->selector, 'getByPrimaryKey'], $id);
    }

    public function getByPrimaryKeys(array $ids)
    {
        return $this->cache->call([$this->selector, 'getByPrimaryKeys'], $ids);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getAll()
    {
        return $this->cache->call([$this->selector, 'getAll']);
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