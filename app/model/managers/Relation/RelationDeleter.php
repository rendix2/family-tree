<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationDeleteRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 21:47
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Relation;

use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultDeleter;

/**
 * Class RelationDeleter
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Relation
 */
class RelationDeleter extends DefaultDeleter implements IRelationDeleter
{
    /**
     * RelationDeleter constructor.
     *
     * @param IStorage      $storage
     * @param Connection    $connection
     * @param RelationTable $table
     */
    public function __construct(
        IStorage $storage,
        Connection $connection,
        RelationTable $table
    ) {
        parent::__construct($connection, $storage, $table);
    }

    public function deleteByMaleId($maleId)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[maleId] = %i', $maleId)
            ->execute();
    }

    public function deleteByFemaleId($femaleId)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[femaleId] = %i', $femaleId)
            ->execute();
    }

    public function deleteByMaleIdAndFemaleId($maleId, $femaleId)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[maleId] = %i', $maleId)
            ->where('[femaleId] = %i', $femaleId)
            ->execute();
    }
}
