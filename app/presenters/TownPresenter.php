<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownPresenter.php
 * User: Tomáš Babický
 * Date: 20.09.2020
 * Time: 0:11
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;

/**
 * Class TownPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class TownPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $manager
     */
    private $manager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * TownPresenter constructor.
     *
     * @param CountryManager $countryManager
     * @param JobManager $jobManager
     */
    public function __construct(
        CountryManager $countryManager,
        JobManager $jobManager,
        PersonManager $personManager,
        TownManager $townManager,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->countryManager = $countryManager;
        $this->jobManager = $jobManager;
        $this->personManager = $personManager;
        $this->manager = $townManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $towns = $this->manager->getAllJoinedCountry();

        $this->template->towns = $towns;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $countries = $this->countryManager->getPairs('name');

        $this['form-countryId']->setItems($countries);

        $this->traitActionEdit($id);
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $birthPersons = [];
            $deathPersons = [];
            $weddings = [];
            $gravedPersons = [];
            $jobs = [];
        } else {
            $birthPersons = $this->personManager->getByBirthTownId($id);
            $deathPersons = $this->personManager->getByDeathTownId($id);
            $gravedPersons = $this->personManager->getByGravedTownId($id);
            $weddings = $this->weddingManager->getByTownId($id);
            $jobs = $this->jobManager->getByTownId($id);

            foreach ($weddings as $wedding) {
                $husband = $this->personManager->getByPrimaryKey($wedding->husbandId);
                $wife = $this->personManager->getByPrimaryKey($wedding->wifeId);

                $wedding->husband = $husband;
                $wedding->wife = $wife;
            }
        }

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
        $this->template->gravedPersons = $gravedPersons;
        $this->template->jobs = $jobs;
        $this->template->town = $this->item;
        $this->template->weddings = $weddings;

        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addSelect('countryId', $this->getTranslator()->translate('town_country'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('town_select_country'))
            ->setRequired('town_country_required');

        $form->addText('name', 'town_name')
            ->setRequired('town_name_required');

        $form->addText('zipCode', 'town_zip');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
