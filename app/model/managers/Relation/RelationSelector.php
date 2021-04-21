<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 21:48
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Relation;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\RelationEntity;

/**
 * Class RelationSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Relation
 */
class RelationSelector extends DefaultSelector implements IRelationSelector
{
    /**
     * RelationSelector constructor.
     *
     * @param Connection     $connection
     * @param RelationTable  $table
     * @param RelationFilter $filter
     */
    public function __construct(
        Connection $connection,
        RelationTable $table,
        RelationFilter $filter
    ) {
        parent::__construct($connection, $table, $filter);
    }

    /**
     * @param $maleId
     *
     * @return RelationEntity[]
     */
    public function getByMaleId($maleId)
    {
        return $this->getAllFluent()
            ->where('[maleId] = %i', $maleId)
            ->execute()
            ->setRowClass(RelationEntity::class)
            ->fetchAll();
    }

    /**
     * @param $femaleId
     *
     * @return RelationEntity[]
     */
    public function getByFemaleId($femaleId)
    {
        return $this->getAllFluent()
            ->where('[femaleId] = %i', $femaleId)
            ->execute()
            ->setRowClass(RelationEntity::class)
            ->fetchAll();
    }

    /**
     * @param $maleId
     * @param $femaleId
     *
     * @return \Dibi\Row|false
     */
    public function getByMaleIdAndFemaleId($maleId, $femaleId)
    {
        return $this->getAllFluent()
            ->where('[maleId] = %i', $maleId)
            ->where('[femaleId] = %i', $femaleId)
            ->fetch();
    }
}