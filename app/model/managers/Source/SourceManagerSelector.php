<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 2:50
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Source;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\SourceEntity;
use Rendix2\FamilyTree\App\Model\Managers\Source\Interfaces\ISourceSelector;
use Rendix2\FamilyTree\App\Model\Table\SourceTable;

/**
 * Class SourceManagerSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Source
 */
class SourceManagerSelector extends DefaultSelector implements ISourceSelector
{
    /**
     * SourceManagerSelector constructor.
     *
     * @param Connection   $connection
     * @param SourceTable  $table
     * @param SourceFilter $sourceFilter
     */
    public function __construct(
        Connection $connection,
        SourceTable $table,
        SourceFilter $sourceFilter
    ) {
        parent::__construct($connection, $table, $sourceFilter);
    }

    /**
     * @param int $personId
     *
     * @return SourceEntity[]
     */
    public function getByPersonId($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->execute()
            ->setRowClass(SourceEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $sourceTypeId
     *
     * @return SourceEntity[]
     */
    public function getBySourceTypeId($sourceTypeId)
    {
        return $this->getAllFluent()
            ->where('[sourceTypeId] = %i', $sourceTypeId)
            ->execute()
            ->setRowClass(SourceEntity::class)
            ->fetchAll();
    }
}