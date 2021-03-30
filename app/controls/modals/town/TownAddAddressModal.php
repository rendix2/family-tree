<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddAddressModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:42
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\AddressForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\AddressSettings;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownAddAddressModal extends Control
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressForm $addressForm
     */
    private $addressForm;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * TownAddAddressModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressForm $addressForm
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,

        AddressForm $addressForm,

        AddressManager $addressManager,
        CountryManager $countryManager,
        TownManager $townManager,

        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;

        $this->addressForm = $addressForm;

        $this->countryManager = $countryManager;
        $this->addressManager = $addressManager;
        $this->townManager = $townManager;

        $this->townSettingsManager = $townSettingsManager;
    }

    public function render()
    {
        $this['townAddAddressForm']->render();
    }

    /**
     * @param int $countryId
     * @param int $townId
     *
     * @return void
     */
    public function handleTownAddAddress($countryId, $townId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');
        $towns = $this->townSettingsManager->getPairsByCountry($countryId);

        $this['townAddAddressForm-_countryId']->setDefaultValue($countryId);

        $this['townAddAddressForm-countryId']->setItems($countries)
            ->setDisabled()
            ->setDefaultValue($countryId);

        $this['townAddAddressForm-_townId']->setDefaultValue($townId);

        $this['townAddAddressForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);

        $presenter->template->modalName = 'townAddAddress';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $formFactory = $this->addressForm;

        $form = $formFactory->create($addressSettings);

        $form->addHidden('_countryId');
        $form->addHidden('_townId');

        $form->onValidate[] = [$this, 'townAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'townAddAddressFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function townAddAddressFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryHiddenControl = $form->getComponent('_countryId');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->setValue($countryHiddenControl->getValue())
            ->validate();

        $towns = $this->townManager->getPairsByCountry($countryControl->getValue());

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $form->removeComponent($countryHiddenControl);
        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getByTownIdCached($values->townId);

        $presenter->template->addresses = $addresses;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('addresses');
    }
}
