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
        $persons = $this->missingManager->getMissingMothers();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderFathers()
    {
        $persons = $this->missingManager->getMissingFathers();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    public function renderParents()
    {
        $persons = $this->missingManager->getMissingParents();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderGenuses()
    {
        $persons = $this->personManager->getByGenusId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderBirthTowns()
    {
        $persons = $this->personManager->getByBirthTownId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderBirths()
    {
        $persons = $this->missingManager->getMissingBirths();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderDeathTowns()
    {
        $persons = $this->personManager->getByDeathTownId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderDeaths()
    {
        $persons = $this->missingManager->getMissingDeaths();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderDates()
    {
        $persons = $this->missingManager->getMissingDates();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderGravedTowns()
    {
        $persons = $this->personManager->getByGravedTownId(null);

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderWeddings()
    {
        $persons = $this->missingManager->getPersonsByMissingWeddings();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderRelations()
    {
        $persons = $this->missingManager->getPersonsByMissingRelations();

        $this->template->persons = $persons;
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
    }

    /**
     * @return void
     */
    public function renderNameWithoutGenus()
    {
        $names = $this->nameManager->getByGenusId(null);

        $this->template->names = $names;
        $this->template->addFilter('Name', new NameFilter());
    }
}
