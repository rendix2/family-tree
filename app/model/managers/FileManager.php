<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileManager.php
 * User: Tomáš Babický
 * Date: 15.12.2020
 * Time: 10:13
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Dibi\Row;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Entities\FileEntity;

/**
 * Class FileManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class FileManager extends CrudManager
{
    /**
     * @param Fluent $query
     */
    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }

    /**
     * @return FileEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(FileEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return FileEntity|false
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(FileEntity::class)
            ->fetch();
    }

    /**
     * @param int $personId
     *
     * @return FileEntity[]|false
     */
    public function getByPersonId($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->execute()
            ->setRowClass(FileEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return FileEntity[]|false
     */
    public function getByPersonIdCached($personId)
    {
        return $this->getCache()->call([$this, 'getByPersonId'], $personId);
    }
}
