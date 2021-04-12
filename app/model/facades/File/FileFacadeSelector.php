<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileFacadeSelector.php
 * User: Tomáš Babický
 * Date: 07.04.2021
 * Time: 0:23
 */

namespace Rendix2\FamilyTree\App\Model\Facades\File;

use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\FileFilter;
use Rendix2\FamilyTree\App\Model\Entities\FileEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Managers\File\Interfaces\IFileSelector;
use Rendix2\FamilyTree\App\Model\Managers\FileManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;

class FileFacadeSelector extends DefaultFacadeSelector implements IFileSelector
{
    /**
     * @var FileManager$fileManager
     */
    private $fileManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * FileFacade constructor.
     *
     * @param FileFilter    $filter
     * @param FileManager   $fileManager
     * @param PersonManager $personManager
     */
    public function __construct(
        FileFilter $filter,
        FileManager $fileManager,
        PersonManager $personManager
    ) {
        parent::__construct($filter);

        $this->fileManager = $fileManager;
        $this->personManager = $personManager;
    }

    /**
     * @param FileEntity[] $files
     * @param PersonEntity[] $persons
     *
     * @return FileEntity[]
     */
    public function join(array $files, array $persons)
    {
        foreach ($files as $file) {
            foreach ($persons as $person) {
                if ($file->_personId === $person->id) {
                    $file->person = $person;

                    break;
                }
            }

            $file->clean();
        }

        return $files;
    }


    public function getByPersonId($personId)
    {
        throw new NotImplementedException();
    }

    public function getByPrimaryKey($id)
    {
        $file = $this->fileManager->select()->getManager()->getByPrimaryKey($id);

        if (!$file) {
            return null;
        }

        $person = $this->personManager->select()->getManager()->getByPrimaryKey($file->_personId);

        return $this->join([$file], [$person])[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
       throw new NotImplementedException();
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getAll()
    {
        $files = $this->fileManager->select()->getCachedManager()->getAll();

        $personIds = $this->fileManager->select()->getManager()->getColumnFluent('personId');

        $persons = $this->personManager->select()->getManager()->getBySubQuery($personIds);

        return $this->join($files, $persons);
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }
}
