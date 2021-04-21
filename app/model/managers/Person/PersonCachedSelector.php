<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonCachedSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces\IPersonSelector;

/**
 * Class PersonCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Person
 * @method PersonSelector getSelector()
 */
class PersonCachedSelector extends DefaultCachedSelector implements IPersonSelector
{
    /**
     * PersonCachedSelector constructor.
     *
     * @param IStorage       $storage
     * @param PersonSelector $selector
     */
    public function __construct(IStorage $storage, PersonSelector $selector)
    {
        parent::__construct($storage, $selector);
    }

    public function getByMotherId($motherId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByMotherId'], $motherId);
    }

    public function getMalesByMotherId($motherId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByMotherId'], $motherId);
    }

    public function getFemalesByMotherId($motherId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByMotherId'], $motherId);
    }

    public function getByFatherId($fatherId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByFatherId'], $fatherId);
    }

    public function getMalesByFatherId($fatherId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getMalesByFatherId'], $fatherId);
    }

    public function getFemalesByFatherId($fatherId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getFemalesByFatherId'], $fatherId);
    }

    public function getByGenusId($genusId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByGenusId'], $genusId);
    }

    public function getByBirthTownId($townId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByBirthTownId'], $townId);
    }

    public function getByBirthAddressId($addressId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByBirthAddressId'], $addressId);
    }

    public function getByDeathTownId($townId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByDeathTownId'], $townId);
    }

    public function getByDeathAddressId($addressId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByDeathAddressId'], $addressId);
    }

    public function getByGravedTownId($townId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByGravedTownId'], $townId);
    }

    public function getByGravedAddressId($addressId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByGravedAddressId'], $addressId);
    }

    public function getAllPairs()
    {
        return $this->getCache()->call([$this->getSelector(), 'getAllPairs']);
    }

    public function getMalesPairs()
    {
        return $this->getCache()->call([$this->getSelector(), 'getMalesPairs']);
    }

    public function getFemalesPairs()
    {
        return $this->getCache()->call([$this->getSelector(), 'getFemalesPairs']);
    }

    public function getBrothers($fatherId, $motherId, $personId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getBrothers'], $fatherId, $motherId, $personId);
    }

    public function getSisters($fatherId, $motherId, $personId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getSisters'], $fatherId, $motherId, $personId);
    }

    public function getSonsByPerson(PersonEntity $person)
    {
        return $this->getCache()->call([$this->getSelector(), 'getSonsByPerson'], $person);
    }

    public function getSonsById($id)
    {
        return $this->getCache()->call([$this->getSelector(), 'getSonsById'], $id);
    }

    public function getDaughtersByPerson(PersonEntity $person)
    {
        return $this->getCache()->call([$this->getSelector(), 'getDaughtersByPerson'], $person);
    }

    public function getDaughtersById($id)
    {
        return $this->getCache()->call([$this->getSelector(), 'getDaughtersById'], $id);
    }

    public function getChildrenByPerson(PersonEntity $person)
    {
        return $this->getCache()->call([$this->getSelector(), 'getChildrenByPerson'], $person);
    }

    public function getChildrenById($id)
    {
        return $this->getCache()->call([$this->getSelector(), 'getChildrenById'], $id);
    }

    public function calculateAgeById($id)
    {
        return $this->getCache()->call([$this->getSelector(), 'calculateAgeById'], $id);
    }

    public function calculateAgeByPerson(PersonEntity $person)
    {
        return $this->getCache()->call([$this->getSelector(), 'calculateAgeByPerson'], $person);
    }
}