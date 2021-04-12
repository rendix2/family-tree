<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingDeleter.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:26
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Wedding;

use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultDeleter;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\Interfaces\IWeddingDeleter;

/**
 * Class WeddingDeleter
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Wedding
 */
class WeddingDeleter extends DefaultDeleter implements IWeddingDeleter
{
    /**
     * WeddingDeleter constructor.
     *
     * @param IStorage     $storage
     * @param Connection   $connection
     * @param WeddingTable $table
     */
    public function __construct(
        IStorage $storage,
        Connection $connection,
        WeddingTable $table
    ) {
        parent::__construct($connection, $storage, $table);
    }

    public function deleteByHusbandId($id)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[husbandId] = %i', $id)
            ->execute();
    }

    public function deleteByWifeId($id)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[wifeId] = %i', $id)
            ->execute();
    }

    public function deleteByHusbandIdAndWifeId($husbandId, $wifeId)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[husbandId] = %i', $husbandId)
            ->where('[wifeId] = %i', $wifeId)
            ->execute();
    }
}
