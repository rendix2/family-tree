<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressCachedSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 18:14
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Address;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\Address\Interfaces\IAddressSelector;

/**
 * Class AddressCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Address
 * @method AddressSelector getSelector()
 */
class AddressCachedSelector extends DefaultCachedSelector implements IAddressSelector
{
    public function __construct(
        IStorage $storage,
        AddressSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function getByCountryId($countryId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByPrimaryKey'], $countryId);
    }

    public function getByTownId($townId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByTownId'], $townId);
    }

    public function getToMap()
    {
        return $this->getCache()->call([$this->getSelector(), 'getToMap']);
    }

    public function getByTownPairs($townId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByTownPairs'], $townId);
    }
}