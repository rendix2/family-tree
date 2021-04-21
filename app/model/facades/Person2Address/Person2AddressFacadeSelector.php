<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2AddressFacadeSelector.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 0:55
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person2Address;

use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\Person2AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\AddressManager;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NSelector;
use Rendix2\FamilyTree\App\Model\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;

class Person2AddressFacadeSelector extends DefaultFacadeSelector implements IM2NSelector
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * PersonAddressFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressManager $addressManager

     * @param Person2AddressManager $person2AddressManager
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        Person2AddressManager $person2AddressManager,
        PersonFacade $personFacade,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressManager = $addressManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
    }

    public function __destruct()
    {
        $this->addressManager = null;
        $this->personManager = null;

        $this->person2AddressManager = null;

        $this->addressFacade = null;
        $this->personFacade = null;

        parent::__destruct();
    }

    /**
     * @param Person2AddressEntity[] $rows
     * @param PersonEntity[] $persons
     * @param AddressEntity[] $addresses
     *
     * @return Person2AddressEntity[]
     */
    private function join(array $rows, array $persons, array $addresses)
    {
        foreach ($rows as $row) {
            foreach ($persons as $person) {
                if ($row->_personId === $person->id) {
                    $row->person = $person;
                    break;
                }
            }

            foreach ($addresses as $address) {
                if ($row->_addressId === $address->id) {
                    $row->address = $address;
                    break;
                }
            }

            $durationEntity = new DurationEntity((array) $row);
            $row->duration = $durationEntity;
        }

        return $rows;
    }

    /**
     * @return Person2AddressEntity[]
     */
    public function getAll()
    {
        $rows = $this->person2AddressManager->select()->getCachedManager()->getAll();

        $personIds = $this->person2AddressManager->select()->getManager()->getColumnFluent('personId');
        $addressIds = $this->person2AddressManager->select()->getManager()->getColumnFluent('addressId');

        $persons = $this->personFacade->select()->getManager()->getBySubQuery($personIds);
        $addresses = $this->addressFacade->select()->getManager()->getBySubQuery($addressIds);

        return $this->join($rows, $persons, $addresses);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getByLeftKey($leftId)
    {
        $relations = $this->person2AddressManager->select()->getManager()->getByLeftKey($leftId);

        if (!$relations) {
            return [];
        }

        $person = $this->personFacade->select()->getManager()->getByPrimaryKey($leftId);
        $addresses = $this->addressFacade->select()->getCachedManager()->getAll();

        return $this->join($relations, [$person], $addresses);
    }

    public function getPairsByLeft($leftId)
    {
        throw new NotImplementedException();
    }

    public function getByLeftKeyJoined($leftId)
    {
        throw new NotImplementedException();
    }

    public function getByRightKey($rightId)
    {
        $relations = $this->person2AddressManager->select()->getManager()->getByRightKey($rightId);

        if (!$relations) {
            return [];
        }

        $personIds = $this->person2AddressManager->select()->getManager()->getColumnFluent('personId');

        $persons = $this->personFacade->select()->getManager()->getBySubQuery($personIds);
        $address = $this->addressFacade->select()->getManager()->getByPrimaryKey($rightId);

        return $this->join($relations, $persons, [$address]);
    }

    /**
     * @param int $addressId
     *
     * @return Person2AddressEntity[]
     */
    public function getByRightManager($addressId)
    {
        $relations = $this->person2AddressManager->select()->getManager()->getByRightKey($addressId);

        if (!$relations) {
            return [];
        }

        $personIds = $this->person2AddressManager->select()->getManager()->getColumnFluent('personId');

        $persons = $this->personManager->select()->getManager()->getBySubQuery($personIds);
        $address = $this->addressManager->select()->getManager()->getByPrimaryKey($addressId);

        return $this->join($relations, $persons, [$address]);
    }

    public function getPairsByRight($rightId)
    {
        throw new NotImplementedException();
    }

    public function getByRightKeyJoined($rightId)
    {
        throw new NotImplementedException();
    }

    public function getByLeftAndRightKey($leftId, $rightId)
    {
        $relation = $this->person2AddressManager->select()->getManager()->getByLeftAndRightKey($leftId, $rightId);

        if (!$relation) {
            return null;
        }

        $person = $this->personFacade->select()->getManager()->getByPrimaryKey($leftId);
        $address = $this->addressFacade->select()->getManager()->getByPrimaryKey($rightId);

        return $this->join([$relation], [$person], [$address])[0];
    }
}