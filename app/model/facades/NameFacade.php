<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameFacade.php
 * User: Tomáš Babický
 * Date: 11.11.2020
 * Time: 17:57
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\GenusEntity;
use Rendix2\FamilyTree\App\Model\Entities\NameEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Class NameFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class NameFacade
{
    use GetIds;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * NameFacade constructor.
     *
     * @param IStorage $storage
     * @param GenusManager $genusManager
     * @param NameManager $nameManager
     * @param PersonManager $personManager
     */
    public function __construct(
        IStorage $storage,
        GenusManager $genusManager,
        NameManager $nameManager,
        PersonManager $personManager
    ) {
        $this->cache = new Cache($storage, self::class);
        $this->genusManager = $genusManager;
        $this->nameManager = $nameManager;
        $this->personManager = $personManager;
    }

    /**
     * @param NameEntity[] $names
     * @param PersonEntity[] $persons
     * @param GenusEntity[] $genuses
     *
     * @return NameEntity[]
     */
    public function join(array $names, array $persons, array $genuses)
    {
        foreach ($names as $name) {
            foreach ($persons as $person) {
                if ($name->_personId === $person->id) {
                    $name->person = $person;
                    break;
                }
            }

            foreach ($genuses as $genus) {
                if ($name->_genusId === $genus->id) {
                    $name->genus = $genus;
                    break;
                }
            }

            $duration = new DurationEntity((array) $name);
            $name->duration = $duration;

            $name->clean();
        }

        return $names;
    }

    /**
     * @return NameEntity[]
     */
    public function getAll()
    {
        $names = $this->nameManager->getAll();

        $personIds = $this->nameManager
            ->getColumnFluent('personId');

        $genusIds = $this->nameManager
            ->getColumnFluent('genusId');

        $persons = $this->personManager->getBySubQuery($personIds);
        $genuses = $this->genusManager->getBySubQuery( $genusIds);

        return $this->join($names, $persons, $genuses);
    }

    /**
     * @return NameEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $nameId
     *
     * @return NameEntity
     */
    public function getByPrimaryKey($nameId)
    {
        $name = $this->nameManager->getByPrimaryKey($nameId);

        if (!$name) {
            return  null;
        }

        $person = $this->personManager->getByPrimaryKey($name->_personId);
        $genus = $this->genusManager->getByPrimaryKey($name->_genusId);

        return $this->join([$name], [$person], [$genus])[0];
    }

    /**
     * @param int $nameId
     *
     * @return NameEntity
     */
    public function getByPrimaryKeyCached($nameId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $nameId);
    }

    /**
     * @param int $personId
     *
     * @return NameEntity[]
     */
    public function getByPerson($personId)
    {
        $names = $this->nameManager->getByPersonId($personId);
        $person = $this->personManager->getByPrimaryKey($personId);
        $genuses = $this->genusManager->getAll();

        return $this->join($names, [$person], $genuses);
    }

    /**
     * @param int $personId
     *
     * @return NameEntity[]
     */
    public function getByPersonCached($personId)
    {
        return $this->cache->call([$this, 'getByPerson'], $personId);
    }

    /**
     * @param int $genusId
     *
     * @return NameEntity[]
     */
    public function getByGenusId($genusId)
    {
        $names = $this->nameManager->getByGenusId($genusId);
        $persons = $this->personManager->getAll();
        $genus = $this->genusManager->getByPrimaryKey($genusId);

        return $this->join($names, $persons, [$genus]);
    }

    /**
     * @param int $genusId
     *
     * @return NameEntity[]
     */
    public function getByGenusIdCached($genusId)
    {
       return $this->cache->call([$this, 'getByGenusId'], $genusId);
    }
}
