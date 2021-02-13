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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\TownForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobSettingsFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownSettingsFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownAddAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownAddCountryModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownAddJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownAddWeddingModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownDeleteAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownDeletePersonBirthModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownDeletePersonDeathModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownDeletePersonGravedModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownDeleteTownJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownDeleteWeddingModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownDeleteTownFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownDeleteTownFromListModal;

/**
 * Class TownPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class TownPresenter extends BasePresenter
{
    use TownAddCountryModal;

    use TownAddAddressModal;
    use TownDeleteAddressModal;

    use TownAddWeddingModal;
    use TownDeleteWeddingModal;

    use TownAddJobModal;
    use TownDeleteTownJobModal;

    use TownDeleteTownFromEditModal;
    use TownDeleteTownFromListModal;

    use TownDeletePersonBirthModal;
    use TownDeletePersonDeathModal;
    use TownDeletePersonGravedModal;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobSettingsFacade $jobSettingsFacade
     */
    private $jobSettingsFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownSettingsFacade $townSettingsFacade
     */
    private $townSettingsFacade;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * TownPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param JobFacade $jobFacade
     * @param JobSettingsFacade $jobSettingsFacade
     * @param JobManager $jobManager
     * @param JobSettingsManager $jobSettingsManager
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param TownFacade $townFacade
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        JobFacade $jobFacade,
        JobSettingsFacade $jobSettingsFacade,
        JobManager $jobManager,
        JobSettingsManager $jobSettingsManager,
        PersonFacade $personFacade,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        TownFacade $townFacade,
        TownSettingsFacade $townSettingsFacade,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager,
        WeddingFacade $weddingFacade,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->addressFacade = $addressFacade;

        $this->countryManager = $countryManager;

        $this->jobFacade = $jobFacade;
        $this->jobSettingsFacade = $jobSettingsFacade;
        $this->jobManager = $jobManager;
        $this->jobSettingsManager = $jobSettingsManager;

        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;

        $this->townFacade = $townFacade;
        $this->townSettingsFacade = $townSettingsFacade;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;

        $this->weddingFacade = $weddingFacade;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $towns = $this->townSettingsFacade->getAllCached();

        $this->template->towns = $towns;

        $this->template->addFilter('country', new CountryFilter());
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $countries = $this->countryManager->getPairsCached('name');

        $this['townForm-countryId']->setItems($countries);

        if ($id !== null) {
            $town = $this->townFacade->getByPrimaryKey($id);

            if (!$town) {
                $this->error('Item not found.');
            }

            $this['townForm-countryId']->setDefaultValue($town->country->id);
            $this['townForm']->setDefaults((array) $town);
        }
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $town = new TownEntity([]);

            $birthPersons = [];
            $deathPersons = [];
            $weddings = [];
            $gravedPersons = [];
            $jobs = [];
            $addresses = [];
        } else {
            $town = $this->townFacade->getByPrimaryKey($id);

            $birthPersons = $this->personSettingsManager->getByBirthTownId($id);
            $deathPersons = $this->personSettingsManager->getByDeathTownId($id);
            $gravedPersons = $this->personSettingsManager->getByGravedTownId($id);
            $weddings = $this->weddingFacade->getByTownIdCached($id);
            $jobs = $this->jobSettingsFacade->getByTownIdCached($id);
            $addresses = $this->addressFacade->getByTownIdCached($id);
        }

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
        $this->template->gravedPersons = $gravedPersons;
        $this->template->jobs = $jobs;
        $this->template->town = $town;
        $this->template->weddings = $weddings;
        $this->template->addresses = $addresses;

        $this->template->addFilter('job', new JobFilter($this->getHttpRequest()));
        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @return Form
     */
    public function createComponentTownForm()
    {
        $formFactory = new TownForm($this->getTranslator());
        $form = $formFactory->create();

        $form->onSuccess[] = [$this, 'townFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->townManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('town_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->townManager->add($values);

            $this->flashMessage('town_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Town:edit', $id);
    }
}
