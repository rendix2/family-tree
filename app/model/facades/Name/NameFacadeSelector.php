<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameFacadeSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:10
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Name;


use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\GenusEntity;
use Rendix2\FamilyTree\App\Model\Entities\NameEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Managers\GenusManager;
use Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces\INameSelector;
use Rendix2\FamilyTree\App\Model\Managers\NameManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;

class NameFacadeSelector extends DefaultFacadeSelector implements INameSelector
{
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
     * @param GenusManager  $genusManager
     * @param NameFilter    $nameFilter
     * @param NameManager   $nameManager
     * @param PersonManager $personManager
     */
    public function __construct(
        GenusManager $genusManager,
        NameFilter $nameFilter,
        NameManager $nameManager,
        PersonManager $personManager
    ) {
        parent::__construct($nameFilter);

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
     * @param int $personId
     *
     * @return NameEntity[]
     */
    public function getByPersonId($personId)
    {
        $names = $this->nameManager->select()->getManager()->getByPersonId($personId);
        $person = $this->personManager->select()->getManager()->getByPrimaryKey($personId);

        $genusIds = $this->getIds($names, '_genusId');

        $genuses = $this->genusManager->select()->getManager()->getByPrimaryKeys($genusIds);

        return $this->join($names, [$person], $genuses);
    }

    public function getByGenusId($genusId)
    {
        $names = $this->nameManager->select()->getManager()->getByGenusId($genusId);
        $persons = $this->personManager->select()->getCachedManager()->getAll();
        $genus = $this->genusManager->select()->getManager()->getByPrimaryKey($genusId);

        return $this->join($names, $persons, [$genus]);
    }

    /**
     * @param int $id
     *
     * @return NameEntity
     */
    public function getByPrimaryKey($id)
    {
        $name = $this->nameManager->select()->getManager()->getByPrimaryKey($id);

        if (!$name) {
            return  null;
        }

        $person = $this->personManager->select()->getManager()->getByPrimaryKey($name->_personId);
        $genus = $this->genusManager->select()->getManager()->getByPrimaryKey($name->_genusId);

        return $this->join([$name], [$person], [$genus])[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        throw new NotImplementedException();
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    /**
     * @return NameEntity[]
     */
    public function getAll()
    {
        $names = $this->nameManager->select()->getCachedManager()->getAll();

        $personIds = $this->nameManager->select()->getManager()->getColumnFluent('personId');

        $genusIds = $this->nameManager->select()->getManager()->getColumnFluent('genusId');

        $persons = $this->personManager->select()->getManager()->getBySubQuery($personIds);
        $genuses = $this->genusManager->select()->getManager()->getBySubQuery( $genusIds);

        return $this->join($names, $persons, $genuses);
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