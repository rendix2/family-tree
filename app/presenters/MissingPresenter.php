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
use Rendix2\FamilyTree\App\Managers\PersonManager;

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
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * MissingPresenter constructor.
     *
     * @param MissingManager $missingManager
     * @param NameManager $nameManager
     * @param PersonManager $personManager
     */
    public function __construct(
        MissingManager $missingManager,
        NameManager $nameManager,
        PersonManager $personManager
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
        $persons = $this->personManager->getMissingMothers();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderFathers()
    {
        $persons = $this->personManager->getMissingFathers();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    public function renderParents()
    {
        $persons = $this->personManager->getMissingParents();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderGenuses()
    {
        $persons = $this->personManager->getByGenusId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderBirthPlaces()
    {
        $persons = $this->personManager->getByBirthPlaceId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderBirths()
    {
        $persons = $this->personManager->getMissingBirths();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderDeathPlaces()
    {
        $persons = $this->personManager->getByDeathPlaceId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderDeaths()
    {
        $persons = $this->personManager->getMissingDeaths();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderDates()
    {
        $persons = $this->personManager->getMissingDates();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderGravedPlaces()
    {
        $persons = $this->personManager->getByGravedPlaceId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderWeddings()
    {
        $persons = $this->missingManager->getPersonsByMissingWeddings();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderRelations()
    {
        $persons = $this->missingManager->getPersonsByMissingRelations();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderNameWithoutGenus()
    {
        $names = $this->nameManager->getByGenusId(null);

        $this->template->names = $names;
        $this->template->addFilter('Name', new NameFilter($this->getTranslator()));
    }
}
