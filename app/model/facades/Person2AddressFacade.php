<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:40
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Person2Address\Person2AddressFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Model\Tables\Person2AddressTable;

/**
 * Class Person2AddressFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class Person2AddressFacade extends DefaultFacade
{
    /**
     * @var Person2AddressFacadeSelectRepository $person2AddressFacadeSelectRepository
     */
    private $person2AddressFacadeSelectRepository;

    /**
     * Person2AddressFacade constructor.
     *
     * @param DefaultContainer                     $defaultContainer
     * @param Person2AddressTable                  $person2AddressTable
     * @param Person2AddressManager                $person2AddressManager
     * @param Person2AddressFacadeSelectRepository $person2AddressFacadeSelectRepository
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        Person2AddressTable $person2AddressTable,
        Person2AddressManager $person2AddressManager,
        Person2AddressFacadeSelectRepository $person2AddressFacadeSelectRepository
    ) {
        parent::__construct($defaultContainer, $person2AddressTable, $person2AddressManager);

        $this->person2AddressFacadeSelectRepository = $person2AddressFacadeSelectRepository;
    }

    /**
     * @return Person2AddressFacadeSelectRepository
     */
    public function select()
    {
        return $this->person2AddressFacadeSelectRepository;
    }
}
