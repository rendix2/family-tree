<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingCachedSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:21
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Wedding;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\Interfaces\IWeddingSelector;

/**
 * Class WeddingCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Wedding
 */
class WeddingCachedSelector extends DefaultCachedSelector implements IWeddingSelector
{
    /**
     * WeddingCachedSelector constructor.
     *
     * @param IStorage        $storage
     * @param WeddingSelector $selector
     */
    public function __construct(
        IStorage $storage,
        WeddingSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function getAllByHusbandId($husbandId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getAllByHusbandId'], $husbandId);
    }

    public function getAllByWifeId($wifeId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getAllByWifeId'], $wifeId);
    }

    public function getByWifeIdAndHusbandId($wifeId, $husbandId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByWifeIdAndHusbandId'], $wifeId, $husbandId);
    }

    public function getByTownId($townId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByTownId'], $townId);
    }

    public function getByAddressId($addressId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByAddressId'], $addressId);
    }
}
