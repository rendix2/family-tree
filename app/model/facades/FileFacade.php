<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileFacade.php
 * User: Tomáš Babický
 * Date: 15.12.2020
 * Time: 10:33
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\FileManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\FileEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Class FileFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class FileFacade
{
    /**
     * @var Cache $cache
     */
    private $cache;

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
     * @param IStorage $storage
     * @param FileManager $fileManager
     * @param PersonManager $personManager
     */
    public function __construct(
        IStorage $storage,
        FileManager $fileManager,
        PersonManager $personManager
    ) {
        $this->cache = new Cache($storage, self::class);
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

    /**
     * @return FileEntity[]
     */
    public function getAll()
    {
        $files = $this->fileManager->getAll();

        $personIds = $this->fileManager->getColumnFluent('personId');

        $persons = $this->personManager->getBySubQuery($personIds);

        return $this->join($files, $persons);
    }

    /**
     * @return FileEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $fileId
     *
     * @return FileEntity|null
     */
    public function getByPrimaryKey($fileId)
    {
        $file = $this->fileManager->getByPrimaryKey($fileId);

        if (!$file) {
            return null;
        }

        $person = $this->personManager->getByPrimaryKey($file->_personId);

        return $this->join([$file], [$person])[0];
    }

    /**
     * @param int $fileId
     *
     * @return FileEntity|null
     */
    public function getByPrimaryKeyCached($fileId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $fileId);
    }
}
