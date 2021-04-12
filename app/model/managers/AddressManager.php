<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:04
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Address\AddressSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Address\AddressTable;

/**
 * Class AddressManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class AddressManager extends CrudManager
{
    /**
     * @var AddressSelectRepository $addressSelectRepository
     */
    private $addressSelectRepository;

    /**
     * AddressManager constructor.
     *
     * @param AddressFilter           $addressFilter
     * @param AddressTable            $table
     * @param AddressSelectRepository $addressSelectRepository
     * @param DefaultContainer        $defaultContainer
     */
    public function __construct(
        AddressFilter $addressFilter,
        AddressTable $table,
        AddressSelectRepository $addressSelectRepository,
        DefaultContainer $defaultContainer
    ) {
        parent::__construct($defaultContainer, $table, $addressFilter);

        $this->addressSelectRepository = $addressSelectRepository;
    }

    /**
     * @return AddressSelectRepository
     */
    public function select()
    {
        return $this->addressSelectRepository;
    }
}
