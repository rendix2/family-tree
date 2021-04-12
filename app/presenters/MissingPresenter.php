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

use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\MissingManager;
use Rendix2\FamilyTree\App\Model\Managers\NameManager;

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
     * @param NameManager    $nameContainer
     * @param PersonManager  $personManager
     */
    public function __construct(
        MissingManager $missingManager,
        NameManager $nameContainer,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->missingManager = $missingManager;
        $this->nameManager = $nameContainer;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderMothers()
    {
        $persons = $this->missingManager->getMissingMothers();

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderFathers()
    {
        $persons = $this->missingManager->getMissingFathers();

        $this->template->persons = $persons;
    }

    public function renderParents()
    {
        $persons = $this->missingManager->getMissingParents();

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderGenuses()
    {
        $persons = $this->personManager->select()->getCachedManager()->getByGenusId(null);

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderBirthTowns()
    {
        $persons = $this->personManager->select()->getCachedManager()->getByBirthTownId(null);

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderBirths()
    {
        $persons = $this->missingManager->getMissingBirths();

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderDeathTowns()
    {
        $persons = $this->personManager->select()->getCachedManager()->getByDeathTownId(null);

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderDeaths()
    {
        $persons = $this->missingManager->getMissingDeaths();

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderDates()
    {
        $persons = $this->missingManager->getMissingDates();

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderGravedTowns()
    {
        $persons = $this->personManager->select()->getCachedManager()->getByGravedTownId(null);

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderWeddings()
    {
        $persons = $this->missingManager->getPersonsByMissingWeddings();

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderRelations()
    {
        $persons = $this->missingManager->getPersonsByMissingRelations();

        $this->template->persons = $persons;
    }

    /**
     * @return void
     */
    public function renderNameWithoutGenus()
    {
        $names = $this->nameManager->select()->getCachedManager()->getByGenusId(null);

        $this->template->names = $names;
    }
}
