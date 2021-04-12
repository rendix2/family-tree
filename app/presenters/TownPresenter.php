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
use Rendix2\FamilyTree\App\Controls\Forms\TownForm;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Container\TownModalContainer;
use Rendix2\FamilyTree\App\Model\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;

/**
 * Class TownPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class TownPresenter extends BasePresenter
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownForm $townForm
     */
    private $townForm;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownModalContainer $townModalContainer
     */
    private $townModalContainer;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * TownPresenter constructor.
     *
     * @param AddressFacade      $addressFacade
     * @param CountryManager     $countryManager
     * @param JobFacade          $jobFacade
     * @param PersonManager      $personManager
     * @param TownFacade         $townFacade
     * @param TownForm           $townForm
     * @param TownManager        $townManager
     * @param TownModalContainer $townModalContainer
     * @param WeddingFacade      $weddingFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        JobFacade $jobFacade,
        PersonManager $personManager,
        TownFacade $townFacade,
        TownForm $townForm,
        TownManager $townManager,
        TownModalContainer $townModalContainer,
        WeddingFacade $weddingFacade
    ) {
        parent::__construct();

        $this->townForm = $townForm;

        $this->townModalContainer = $townModalContainer;

        $this->addressFacade = $addressFacade;
        $this->jobFacade = $jobFacade;
        $this->townFacade = $townFacade;
        $this->weddingFacade = $weddingFacade;

        $this->countryManager = $countryManager;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $towns = $this->townFacade->select()->getSettingsCachedManager()->getAll();

        $this->template->towns = $towns;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $this['townForm-countryId']->setItems($countries);

        if ($id !== null) {
            $town = $this->townFacade->select()->getCachedManager()->getByPrimaryKey($id);

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
            $town = $this->townFacade->select()->getCachedManager()->getByPrimaryKey($id);

            $birthPersons = $this->personManager->select()->getSettingsCachedManager()->getByBirthTownId($id);
            $deathPersons = $this->personManager->select()->getSettingsCachedManager()->getByDeathTownId($id);
            $gravedPersons = $this->personManager->select()->getSettingsCachedManager()->getByGravedTownId($id);
            $weddings = $this->weddingFacade->select()->getCachedManager()->getByTownId($id);
            $jobs = $this->jobFacade->select()->getSettingsCachedManager()->getByTownId($id);
            $addresses = $this->addressFacade->select()->getCachedManager()->getByTownId($id);
        }

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
        $this->template->gravedPersons = $gravedPersons;
        $this->template->jobs = $jobs;
        $this->template->town = $town;
        $this->template->weddings = $weddings;
        $this->template->addresses = $addresses;

    }

    /**
     * @return Form
     */
    public function createComponentTownForm()
    {
        $form = $this->townForm->create();

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
            $this->townManager->update()->updateByPrimaryKey($id, (array) $values);

            $this->flashMessage('town_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->townManager->insert()->insert((array) $values);

            $this->flashMessage('town_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Town:edit', $id);
    }

    public function createComponentTownAddCountryModal()
    {
        return $this->townModalContainer->getTownAddCountryModalFactory()->create();
    }

    public function createComponentTownAddAddressModal()
    {
        return $this->townModalContainer->getTownAddAddressModalFactory()->create();
    }

    public function createComponentTownDeleteAddressModal()
    {
        return $this->townModalContainer->getTownDeleteAddressModalFactory()->create();
    }

    public function createComponentTownAddWeddingModal()
    {
        return $this->townModalContainer->getTownAddWeddingModalFactory()->create();
    }

    public function createComponentTownDeleteWeddingModal()
    {
        return $this->townModalContainer->getTownDeleteWeddingModalFactory()->create();
    }

    public function createComponentTownAddJobModal()
    {
        return $this->townModalContainer->getTownAddJobModalFactory()->create();
    }

    public function createComponentTownDeleteTownJobModal()
    {
        return $this->townModalContainer->getTownDeleteTownJobModalFactory()->create();
    }

    public function createComponentTownDeleteTownFromEditModal()
    {
        return $this->townModalContainer->getTownDeleteTownFromEditModalFactory()->create();
    }

    public function createComponentTownDeleteTownFromListModal()
    {
        return $this->townModalContainer->getTownDeleteTownFromListModalFactory()->create();
    }

    public function createComponentTownDeleteBirthPersonModal()
    {
        return $this->townModalContainer->getTownDeleteBirthPersonModalFactory()->create();
    }

    public function createComponentTownDeleteDeathPersonModal()
    {
        return $this->townModalContainer->getTownDeleteDeathPersonModalFactory()->create();
    }

    public function createComponentTownDeleteGravedPersonModal()
    {
        return $this->townModalContainer->getTownDeleteGravedPersonModalFactory()->create();
    }
}
