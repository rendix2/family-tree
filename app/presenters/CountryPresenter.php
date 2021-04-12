<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryPresenter.php
 * User: Tomáš Babický
 * Date: 04.10.2020
 * Time: 21:48
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\CountryForm;
use Rendix2\FamilyTree\App\Controls\Modals\Country\Container\CountryModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryAddAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryAddTownModal;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteCountryFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteCountryFromListModal;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteTownModal;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;

/**
 * Class CountryPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class CountryPresenter extends BasePresenter
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var CountryModalContainer $countryModalContainer
     */
    private $countryModalContainer;

    /**
     * @var CountryForm $countryForm
     */
    private $countryForm;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * CountryPresenter constructor.
     *
     * @param CountryModalContainer $countryModalContainer
     * @param AddressFacade         $addressFacade
     * @param CountryForm           $countryForm
     * @param CountryManager        $countryManager
     * @param TownManager           $townManager
     */
    public function __construct(
        CountryModalContainer $countryModalContainer,
        AddressFacade $addressFacade,
        CountryForm $countryForm,
        CountryManager $countryManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->countryModalContainer = $countryModalContainer;

        $this->countryForm = $countryForm;

        $this->addressFacade = $addressFacade;

        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $countries = $this->countryManager->select()->getCachedManager()->getAll();

        $this->template->countries = $countries;
    }

    /**
     * @param int|null $id countryId
     */
    public function actionEdit($id = null)
    {
        if ($id !== null) {
            $country = $this->countryManager->select()->getCachedManager()->getByPrimaryKey($id);

            if (!$country) {
                $this->error('Item not found.');
            }

            $this['countryForm']->setDefaults((array) $country);
        }
    }

    /**
     * @param int|null $id countryId
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $towns = [];
            $addresses = [];
        } else {
            $towns = $this->townManager->select()->getSettingsCachedManager()->getAllByCountry($id);
            $addresses = $this->addressFacade->select()->getCachedManager()->getByCountryId($id);
        }

        $country = $this->countryManager->select()->getCachedManager()->getByPrimaryKey($id);

        $this->template->towns = $towns;
        $this->template->addresses = $addresses;
        $this->template->country = $country;
    }

    /**
     * @return Form
     */
    public function createComponentCountryForm()
    {
        $form = $this->countryForm->create();

        $form->onSuccess[] = [$this, 'countryFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function countryFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->countryManager->update()->updateByPrimaryKey($id,(array) $values);

            $this->flashMessage('country_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->countryManager->insert()->insert((array) $values);

            $this->flashMessage('country_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Country:edit', $id);
    }

    /**
     * @return CountryDeleteCountryFromListModal
     */
    public function createComponentCountryDeleteCountryFromListModal()
    {
        return $this->countryModalContainer->getCountryDeleteCountryFromListModalFactory()->create();
    }

    /**
     * @return CountryDeleteCountryFromEditModal
     */
    public function createComponentCountryDeleteCountryFromEditModal()
    {
        return $this->countryModalContainer->getCountryDeleteCountryFromEditModalFactory()->create();
    }

    /**
     * @return CountryAddAddressModal
     */
    public function createComponentCountryAddAddressModal()
    {
        return $this->countryModalContainer->getCountryAddAddressModalFactory()->create();
    }

    /**
     * @return CountryAddTownModal
     */
    public function createComponentCountryAddTownModal()
    {
        return $this->countryModalContainer->getCountryAddTownModalFactory()->create();
    }

    /**
     * @return CountryDeleteTownModal
     */
    public function createComponentCountryDeleteTownModal()
    {
        return $this->countryModalContainer->getCountryDeleteTownModalFactory()->create();
    }

    /**
     * @return CountryDeleteAddressModal
     */
    public function createComponentCountryDeleteAddressModal()
    {
        return $this->countryModalContainer->getCountryDeleteAddressModalFactory()->create();
    }
}
