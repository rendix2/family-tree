<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:54
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Name;


use dibi;
use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\NameEntity;
use Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces\INameSelector;
use Rendix2\FamilyTree\App\Model\Tables\NameTable;

/**
 * Class NameSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Name
 */
class NameSelector extends DefaultSelector implements INameSelector
{
    /**
     * NameSelector constructor.
     *
     * @param Connection $connection
     * @param NameTable  $table
     * @param NameFilter $filter
     */
    public function __construct(
        Connection $connection,
        NameTable $table,
        NameFilter $filter
    ) {
        parent::__construct($connection, $table, $filter);
    }

    /**
     * @param $personId
     *
     * @return NameEntity[]
     */
    public function getByPersonId($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->orderBy('dateSince', dibi::ASC)
            ->execute()
            ->setRowClass(NameEntity::class)
            ->fetchAll();
    }

    /**
     * @param $genusId
     *
     * @return NameEntity[]
     */
    public function getByGenusId($genusId)
    {
        return $this->getAllFluent()
            ->where('[genusId] = %i', $genusId)
            ->execute()
            ->setRowClass(NameEntity::class)
            ->fetchAll();
    }
}
