<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2AddressFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 0:57
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person2Address;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class Person2AddressFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person2Address
 */
class Person2AddressFacadeSelectRepository implements ISelectRepository
{
    /**
     * @var Person2AddressFacadeCachedSelector $person2AddressFacadeCachedSelector
     */
    private $person2AddressFacadeCachedSelector;

    /**
     * @var Person2AddressFacadeSelector $person2AddressFacadeSelector
     */
    private $person2AddressFacadeSelector;

    /**
     * Person2AddressFacadeSelectRepository constructor.
     *
     * @param Person2AddressFacadeCachedSelector $person2AddressFacadeCachedSelector
     * @param Person2AddressFacadeSelector       $person2AddressFacadeSelector
     */
    public function __construct(
        Person2AddressFacadeCachedSelector $person2AddressFacadeCachedSelector,
        Person2AddressFacadeSelector $person2AddressFacadeSelector
    ) {
        $this->person2AddressFacadeCachedSelector = $person2AddressFacadeCachedSelector;
        $this->person2AddressFacadeSelector = $person2AddressFacadeSelector;
    }

    /**
     * @return Person2AddressFacadeSelector
     */
    public function getManager()
    {
        return $this->person2AddressFacadeSelector;
    }

    /**
     * @return Person2AddressFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->person2AddressFacadeCachedSelector;
    }
}
