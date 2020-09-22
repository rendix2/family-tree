<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Missing.php
 * User: Tomáš Babický
 * Date: 21.09.2020
 * Time: 0:30
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\MissingManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;

/**
 * Class MissingPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class MissingPresenter extends BasePresenter
{
    /**
     * @var MissingManager $missingManager
     */
    private $missingManager;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * MissingPresenter constructor.
     *
     * @param MissingManager $missingManager
     * @param NameManager $nameManager
     * @param PeopleManager $personManager
     */
    public function __construct(
        MissingManager $missingManager,
        NameManager $nameManager,
        PeopleManager $personManager
    ) {
        parent::__construct();

        $this->missingManager = $missingManager;
        $this->nameManager = $nameManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderMothers()
    {
        $persons = $this->personManager->getByMotherId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @return void
     */
    public function renderFathers()
    {
        $persons = $this->personManager->getByFatherId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @return void
     */
    public function renderGenuses()
    {
        $persons = $this->personManager->getByGenusId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @return void
     */
    public function renderBirthPlaces()
    {
        $persons = $this->personManager->getByBirthPlaceId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @return void
     */
    public function renderDeathPlaces()
    {
        $persons = $this->personManager->getByDeathPlaceId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @return void
     */
    public function renderWeddings()
    {
        $persons = $this->missingManager->getPersonsByMissingWeddings();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @return void
     */
    public function renderRelations()
    {
        $persons = $this->missingManager->getPersonsByMissingRelations();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @return void
     */
    public function renderNameWithoutGenus()
    {
        $names = $this->nameManager->getByGenusId(1);

        $this->template->names = $names;
        $this->template->addFilter('Name', new NameFilter());
    }
}
