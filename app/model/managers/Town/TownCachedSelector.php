<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownCachedSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 12:28
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Town;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\Town\Interfaces\ITownSelector;

/**
 * Class TownCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Town
 *
 * @method TownSelector getSelector()
 */
class TownCachedSelector extends DefaultCachedSelector implements ITownSelector
{
    /**
     * TownCachedSelector constructor.
     *
     * @param IStorage     $storage
     * @param TownSelector $selector
     */
    public function __construct(IStorage $storage, TownSelector $selector)
    {
        parent::__construct($storage, $selector);
    }

    public function getPairsByCountry($countryId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getPairsByCountry'], $countryId);
    }

    public function getAllPairs()
    {
        return $this->getCache()->call([$this->getSelector(), 'getAllPairs']);
    }

    public function getAllByCountry($countryId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getAllByCountry'], $countryId);
    }

    public function getToMap()
    {
        return $this->getCache()->call([$this->getSelector(), 'getToMap']);
    }
}
