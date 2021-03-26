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
use Rendix2\FamilyTree\App\Controls\Modals\Country\Container\CountryModalContainer;
use Rendix2\FamilyTree\App\Forms\CountryForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;

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
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var CountryModalContainer $countryModalContainer
     */
    private $countryModalContainer;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * CountryPresenter constructor.
     *
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param CountryModalContainer $countryModalContainer
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        CountryModalContainer $countryModalContainer,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->countryModalContainer = $countryModalContainer;

        $this->addressFacade = $addressFacade;

        $this->countryManager = $countryManager;
        $this->townSettingsManager = $townSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $countries = $this->countryManager->getAllCached();

        $this->template->countries = $countries;
    }

    /**
     * @param int|null $id countryId
     */
    public function actionEdit($id = null)
    {
        if ($id !== null) {
            $country = $this->countryManager->getByPrimaryKeyCached($id);

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
            $towns = $this->townSettingsManager->getAllByCountry($id);
            $addresses = $this->addressFacade->getByCountryIdCached($id);
        }

        $country = $this->countryManager->getByPrimaryKeyCached($id);

        $this->template->towns = $towns;
        $this->template->addresses = $addresses;
        $this->template->country = $country;
    }

    /**
     * @return Form
     */
    public function createComponentCountryForm()
    {
        $formFactory = new CountryForm($this->translator);

        $form = $formFactory->create();
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
            $this->countryManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('country_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->countryManager->add($values);

            $this->flashMessage('country_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Country:edit', $id);
    }

    public function createComponentCountryDeleteCountryFromListModal()
    {
        return $this->countryModalContainer->getCountryDeleteCountryFromListModalFactory()->create();
    }

    public function createComponentCountryDeleteCountryFromEditModal()
    {
        return $this->countryModalContainer->getCountryDeleteCountryFromEditModalFactory()->create();
    }

    public function createComponentCountryAddAddressModal()
    {
        return $this->countryModalContainer->getCountryAddAddressModalFactory()->create();
    }

    public function createComponentCountryAddTownModal()
    {
        return $this->countryModalContainer->getCountryAddTownModalFactory()->create();
    }

    public function createComponentCountryDeleteTownModal()
    {
        return $this->countryModalContainer->getCountryDeleteTownModalFactory()->create();
    }

    public function createComponentCountryDeleteAddressModal()
    {
        return $this->countryModalContainer->getCountryDeleteAddressModalFactory()->create();
    }
}
