<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddAddressModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 1:57
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\AddressForm;
use Rendix2\FamilyTree\App\Controls\Forms\Helpers\FormJsonDataParser;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\AddressSettings;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\AddressManager;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddAddressModal extends Control
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
     * PersonAddAddressModal constructor.
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
    ){
        parent::__construct();

        $this->addressFacade = $addressFacade;

        $this->addressForm = $addressForm;

        $this->addressManager = $addressManager;
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddAddressForm']->render();
    }

    /**
     * @return void
     */
    public function handlePersonAddAddress()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $this['personAddAddressForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'personAddAddress';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
        $presenter->redrawControl('js');
    }

    /**
     * @param int $countryId countryId
     * @param string $formData
     */
    public function handlePersonAddAddressSelectCountry($countryId, $formData)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId']);

        if ($countryId) {
            $this['personAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);

            $towns = $this->townManager->select()->getManager()->getPairsByCountry($countryId);

            $this['personAddAddressForm-townId']->setItems($towns);
        } else {
            $this['personAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['personAddAddressForm-townId']->setItems([]);
        }

        $this['personAddAddressForm']->setDefaults($formDataParsed);

        $presenter->redrawControl('js');
        $presenter->redrawControl('personAddAddressFormWrapper');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('personAddAddressSelectCountry!');

        $formFactory = $this->addressForm;

        $form = $formFactory->create($addressSettings);
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'personAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'personAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function personAddAddressFormValidate(Form $form)
    {
        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();

        $towns = $this->townManager->select()->getManager()->getPairsByCountry($countryControl->getValue());

        $townHiddenControl = $form->getComponent('_townId');
        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->addressManager->insert()->insert((array) $values);

        $addresses = $this->addressFacade->select()->getManager()->getAllPairs();

        $presenter['personForm-birthAddressId']->setItems($addresses);
        $presenter['personForm-deathAddressId']->setItems($addresses);
        $presenter['personForm-gravedAddressId']->setItems($addresses);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->snippets = [
            $presenter['personForm-birthAddressId']->getHtmlId() => (string) $presenter['personForm-birthAddressId']->getControl(),
            $presenter['personForm-deathAddressId']->getHtmlId() => (string) $presenter['personForm-deathAddressId']->getControl(),
            $presenter['personForm-gravedAddressId']->getHtmlId() => (string) $presenter['personForm-gravedAddressId']->getControl(),
        ];

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jsFormCallback');
    }
}
