<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddAddressModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\AddressForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\AddressSettings;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\AddressManager;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class CountryAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country
 */
class CountryAddAddressModal extends Control
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
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * CountryAddAddressModal constructor.
     *
     * @param AddressFacade  $addressFacade
     * @param AddressForm    $addressForm
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param TownManager    $townManager
     */
    public function __construct(
        AddressFacade $addressFacade,

        AddressForm $addressForm,

        AddressManager $addressManager,
        CountryManager $countryManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;

        $this->addressForm = $addressForm;

        $this->addressManager = $addressManager;
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
    }

    public function render()
    {
        $this['countryAddAddressForm']->render();
    }

    /**
     * @param int $countryId
     *
     * @param string $formData
     * @return void
     */
    public function handleCountryAddAddress($countryId, $formData)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');
        $towns = $this->townManager->select()->getCachedManager()->getPairsByCountry($countryId);

        $this['countryAddAddressForm-_countryId']->setDefaultValue($countryId);
        $this['countryAddAddressForm-countryId']->setItems($countries)
            ->setDisabled()
            ->setDefaultValue($countryId);

        $this['countryAddAddressForm-townId']->setItems($towns);

        $presenter->template->modalName = 'countryAddAddress';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryAddAddressForm()
    {
        $addressSettings = new AddressSettings();

        $formFactory = $this->addressForm;

        $form = $formFactory->create($addressSettings);

        $form->addHidden('_countryId');
        $form->addHidden('_townId');

        $form->onValidate[] = [$this, 'countryAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'countryAddAddressFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function countryAddAddressFormValidate(Form $form)
    {
        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $countryHiddenControl = $form->getComponent('_countryId');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->setValue($countryHiddenControl->getValue())
            ->validate();

        $towns = $this->townManager->select()->getManager()->getPairsByCountry($countryHiddenControl->getValue());

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $form->removeComponent($countryHiddenControl);
        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function countryAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        $this->addressManager->insert()->insert((array) $values);

        $addresses = $this->addressFacade->select()->getCachedManager()->getByCountryId($values->countryId);

        $presenter->template->addresses = $addresses;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('addresses');
    }
}
