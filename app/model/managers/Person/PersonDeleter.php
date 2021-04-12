<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleter.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:54
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person;

use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultDeleter;
use Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces\IPersonDeleter;

/**
 * Class PersonDeleter
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Person
 */
class PersonDeleter extends DefaultDeleter implements IPersonDeleter
{
    /**
     * PersonDeleter constructor.
     *
     * @param IStorage    $storage
     * @param Connection  $connection
     * @param PersonTable $table
     */
    public function __construct(
        IStorage $storage,
        Connection $connection,
        PersonTable $table
    ) {
        parent::__construct($connection, $storage, $table);
    }

    public function deleteByMotherId($motherId)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[motherId] = %i', $motherId)
            ->execute();
    }

    public function deleteByFatherId($fatherId)
    {
        $this->deleteAllCache();

        return $this->deleteFluent()
            ->where('[fatherId] = %i', $fatherId)
            ->execute();
    }
}
