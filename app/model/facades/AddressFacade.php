<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 1:26
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\Address\AddressFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Managers\Address\AddressTable;
use Rendix2\FamilyTree\App\Model\Managers\AddressManager;

/**
 * Class AddressFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class AddressFacade extends DefaultFacade
{
    /**
     * @var AddressFacadeSelectRepository $addressFacadeSelectRepository
     */
    private $addressFacadeSelectRepository;

    /**
     * AddressFacade constructor.
     *
     * @param AddressManager                $addressManager
     * @param AddressFacadeSelectRepository $addressFacadeSelectRepository
     * @param AddressTable                  $table
     * @param DefaultContainer              $defaultContainer
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacadeSelectRepository $addressFacadeSelectRepository,
        AddressTable $table,
        DefaultContainer $defaultContainer
    ) {
        parent::__construct($defaultContainer, $table, $addressManager);

        $this->addressFacadeSelectRepository = $addressFacadeSelectRepository;
    }

    public function __destruct()
    {
        $this->addressFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return AddressFacadeSelectRepository
     */
    public function select()
    {
        return $this->addressFacadeSelectRepository;
    }
}
