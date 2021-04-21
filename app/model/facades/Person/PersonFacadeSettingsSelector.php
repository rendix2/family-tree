<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacadeSettingsSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:47
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person;

use Dibi\Fluent;

/**
 * Class PersonFacadeSettingsSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person
 */
class PersonFacadeSettingsSelector extends PersonFacadeSelector
{
    public function getByGenusId($genusId)
    {
        $genusPersons = $this->getPersonManager()->select()->getSettingsCachedManager()->getByGenusId($genusId);
        $persons = $this->getPersonManager()->select()->getSettingsCachedManager()->getAll();

        $towns = $this->getTownFacade()->select()->getSettingsCachedManager()->getAll();
        $addresses = $this->getAddressFacade()->select()->getCachedManager()->getAll();
        $genuses = $this->getGenusManager()->select()->getCachedManager()->getByPrimaryKey($genusId);

        return $this->join($genusPersons, $persons, $towns, $addresses, [$genuses]);
    }

    public function getAll()
    {
        $persons = $this->getPersonManager()->select()->getSettingsManager()->getAll();

        $towns = $this->getTownFacade()->select()->getSettingsCachedManager()->getAll();
        $addresses = $this->getAddressFacade()->select()->getCachedManager()->getAll();
        $genuses = $this->getGenusManager()->select()->getCachedManager()->getAll();

        return $this->join($persons, $persons, $towns, $addresses, $genuses);
    }

    public function getByPrimaryKey($id)
    {
        $person = $this->getPersonManager()->select()->getSettingsManager()->getByPrimaryKey($id);

        if (!$person) {
            return null;
        }

        $parents = $this->getPersonManager()->select()->getSettingsManager()->getByPrimaryKeys(
            [
                $person->_motherId,
                $person->_fatherId
            ]
        );

        $towns = $this->getTownFacade()->select()->getManager()->getByPrimaryKeys(
            [
                $person->_birthTownId,
                $person->_deathTownId,
                $person->_gravedTownId,
            ]
        );

        $addresses = $this->getAddressFacade()->select()->getManager()->getByPrimaryKeys(
            [
                $person->_birthAddressId,
                $person->_deathAddressId,
                $person->_gravedAddressId,
            ]
        );

        $genus = [];

        if ($person->_genusId) {
            $genus[] = $this->getGenusManager()->select()->getManager()->getByPrimaryKey($person->_genusId);
        }

        return $this->join([$person], $parents, $towns, $addresses, $genus)[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        $persons = $this->getPersonManager()->select()->getSettingsManager()->getByPrimaryKeys($ids);

        if (!$persons) {
            return [];
        }

        $personParentsIds = [];
        $townIds = [];
        $addressIds = [];
        $genusIds = [];

        foreach ($persons as $person) {
            $personParentsIds[] = $person->_motherId;
            $personParentsIds[] = $person->_fatherId;

            $townIds[] = $person->_birthTownId;
            $townIds[] = $person->_deathTownId;
            $townIds[] = $person->_gravedTownId;

            $addressIds[] = $person->_birthAddressId;
            $addressIds[] = $person->_deathAddressId;
            $addressIds[] = $person->_gravedAddressId;

            $genusIds[] = $person->_genusId;
        }

        $townIds = array_unique($townIds);

        $parents = $this->getPersonManager()->select()->getSettingsManager()->getByPrimaryKeys($personParentsIds);

        foreach ($parents as $parent) {
            $townIds[] = $parent->_birthTownId;
            $townIds[] = $parent->_deathTownId;
            $townIds[] = $parent->_gravedTownId;

            $addressIds[] = $parent->_birthAddressId;
            $addressIds[] = $parent->_deathAddressId;
            $addressIds[] = $parent->_gravedAddressId;

            $genusIds[] = $parent->_genusId;
        }

        $townIds = array_unique($townIds);
        $addressIds = array_unique($addressIds);

        $towns = $this->getTownFacade()->select()->getManager()->getByPrimaryKeys($townIds);
        $addresses = $this->getAddressFacade()->select()->getManager()->getByPrimaryKeys($addressIds);
        $genuses = $this->getGenusManager()->select()->getManager()->getByPrimaryKeys($genusIds);

        return $this->join($persons, $parents, $towns, $addresses, $genuses);
    }

    public function getBySubQuery(Fluent $query)
    {
        $persons = $this->getPersonManager()->select()->getSettingsManager()->getBySubQuery($query);

        $towns = $this->getTownFacade()->select()->getSettingsCachedManager()->getAll();
        $addresses = $this->getAddressFacade()->select()->getCachedManager()->getAll();
        $genuses = $this->getGenusManager()->select()->getCachedManager()->getAll();

        return $this->join($persons, $persons, $towns, $addresses, $genuses);
    }
}
