<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameDeleter.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 23:00
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Name;

use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultDeleter;
use Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces\INameDeleter;
use Rendix2\FamilyTree\App\Model\Tables\NameTable;

/**
 * Class NameDeleter
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Name
 */
class NameDeleter extends DefaultDeleter implements INameDeleter
{
    /**
     * NameDeleter constructor.
     *
     * @param IStorage   $storage
     * @param Connection $connection
     * @param NameTable  $table
     */
    public function __construct(
        IStorage $storage,
        Connection $connection,
        NameTable $table
    ) {
        parent::__construct($connection, $storage, $table);
    }

    public function deleteByPersonId($personId)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[personId] = %i', $personId)
            ->execute();
    }
}
