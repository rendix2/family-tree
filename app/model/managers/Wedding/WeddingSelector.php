<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:20
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Wedding;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\Interfaces\IWeddingSelector;

/**
 * Class WeddingSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Wedding
 */
class WeddingSelector extends DefaultSelector implements IWeddingSelector
{
    /**
     * WeddingSelector constructor.
     *
     * @param Connection    $connection
     * @param WeddingTable  $table
     * @param WeddingFilter $filter
     */
    public function __construct(
        Connection $connection,
        WeddingTable $table,
        WeddingFilter $filter
    ) {
        parent::__construct($connection, $table, $filter);
    }

    public function getAllByHusbandId($husbandId)
    {
        return $this->getAllFluent()
            ->where('[husbandId] = %i', $husbandId)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }

    public function getAllByWifeId($wifeId)
    {
        return $this->getAllFluent()
            ->where('[wifeId] = %i', $wifeId)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }

    public function getByWifeIdAndHusbandId($wifeId, $husbandId)
    {
        return $this->getAllFluent()
            ->where('[wifeId] = %i', $wifeId)
            ->where('[husbandId] = %i', $husbandId)
            ->fetch();
    }

    public function getByTownId($townId)
    {
        return $this->getAllFluent()
            ->where('[townId] = %i', $townId)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }

    public function getByAddressId($addressId)
    {
        return $this->getAllFluent()
            ->where('[addressId] = %i', $addressId)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }
}