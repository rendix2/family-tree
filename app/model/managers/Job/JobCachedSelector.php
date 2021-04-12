<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobCachedSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 15:16
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Job;


use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\Job\Interfaces\IJobSelector;

/**
 * Class JobCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Job
 */
class JobCachedSelector extends DefaultCachedSelector implements IJobSelector
{
    /**
     * JobCachedSelector constructor.
     *
     * @param IStorage    $storage
     * @param JobSelector $selector
     */
    public function __construct(
        IStorage $storage,
        JobSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function getByTownId($townId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByTownId'], $townId);
    }

    public function getByAddressId($addressId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByAddressId'], $addressId);
    }

    public function geAllPairs()
    {
        return $this->getCache()->call([$this->getSelector(), 'geAllPairs']);
    }
}