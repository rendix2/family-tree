<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceManager.php
 * User: Tomáš Babický
 * Date: 01.10.2020
 * Time: 23:40
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Row;
use Rendix2\FamilyTree\App\Model\Entities\SourceEntity;

/**
 * Class SourceManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class SourceManager extends CrudManager
{
    /**
     * @return SourceEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(SourceEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return SourceEntity|false
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(SourceEntity::class)
            ->fetch();
    }

    /**
     * @return Row[]
     */
    public function getAllJoinedPersonJoinedSourceType()
    {
        return $this->getAllFluent()
            ->as($this->getTableAlias())
            ->innerJoin(Tables::SOURCE_TYPE_TABLE)
            ->as('st')
            ->on('[s.sourceTypeId] = [st.id]')
            ->innerJoin(Tables::PERSON_TABLE)
            ->as('p')
            ->on('[s.personId] = [p.id]')
            ->fetchAll();
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
     * @return Row[]
     */
    public function getBySourceTypeId($sourceTypeId)
    {
        return $this->getAllFluent()
            ->where('[sourceTypeId] = %i', $sourceTypeId)
            ->execute()
            ->setRowClass(SourceEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return Row[]
     */
    public function getByPersonIdJoinedSourceType($personId)
    {
        return $this->getAllFluent()
            ->as($this->getTableAlias())
            ->where('[personId] = %i', $personId)
            ->innerJoin(Tables::SOURCE_TYPE_TABLE)
            ->as('st')
            ->on('[s.sourceTypeId] = [st.id]')
            ->fetchAll();
    }
}
