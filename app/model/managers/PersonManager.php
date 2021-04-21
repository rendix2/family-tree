<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 21:02
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Person\PersonDeleter;
use Rendix2\FamilyTree\App\Model\Managers\Person\PersonSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Person\PersonTable;

/**
 * Class PersonManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class PersonManager extends CrudManager
{
    /**
     * @var PersonDeleter $personDeleter
     */
    private $personDeleter;

    /**
     * @var PersonSelectRepository $personSelectRepository
     */
    private $personSelectRepository;

    /**
     * PersonManager constructor.
     *
     * @param DefaultContainer       $defaultContainer
     * @param PersonDeleter          $personDeleter
     * @param PersonSelectRepository $personSelectRepository
     * @param PersonFilter           $personFilter
     * @param PersonTable            $personTable
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        PersonDeleter $personDeleter,
        PersonSelectRepository $personSelectRepository,
        PersonFilter $personFilter,
        PersonTable $personTable
    ) {
        parent::__construct($defaultContainer, $personTable, $personFilter);

        $this->personDeleter = $personDeleter;
        $this->personSelectRepository = $personSelectRepository;
    }

    public function __destruct()
    {
        $this->personDeleter = null;
        $this->personSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return PersonDeleter
     */
    public function delete()
    {
        return $this->personDeleter;
    }

    /**
     * @return PersonSelectRepository
     */
    public function select()
    {
        return $this->personSelectRepository;
    }
}
