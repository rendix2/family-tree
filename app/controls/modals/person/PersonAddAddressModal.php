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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
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
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * PersonAddAddressModal constructor.
     *
     * @param ITranslator $translator
     * @param AddressFacade $addressFacade
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        ITranslator $translator,
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        CountryManager $countryManager,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager
    ){
        parent::__construct();

        $this->translator = $translator;

        $this->addressFacade = $addressFacade;

        $this->addressManager = $addressManager;
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;

        $this->townSettingsManager = $townSettingsManager;
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
        $countries = $this->countryManager->getPairs('name');

        $this['personAddAddressForm-countryId']->setItems($countries);

        $this->presenter->template->modalName = 'personAddAddress';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
        $this->presenter->redrawControl('js');
    }

    /**
     * @param int $countryId countryId
     * @param string $formData
     */
    public function handlePersonAddAddressSelectCountry($countryId, $formData)
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->presenter->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId']);

        if ($countryId) {
            $this['personAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);

            $towns = $this->townSettingsManager->getPairsByCountry($countryId);

            $this['personAddAddressForm-townId']->setItems($towns);
        } else {
            $this['personAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['personAddAddressForm-townId']->setItems([]);
        }

        $this['personAddAddressForm']->setDefaults($formDataParsed);

        $this->presenter->redrawControl('js');
        $this->presenter->redrawControl('personAddAddressFormWrapper');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('personAddAddressSelectCountry!');

        $formFactory = new AddressForm($this->translator, $addressSettings);

        $form = $formFactory->create();
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
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();

        $towns = $this->townManager->getPairsByCountry($countryControl->getValue());

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
        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getAllPairs();

        $this->presenter['personForm-birthAddressId']->setItems($addresses);
        $this->presenter['personForm-deathAddressId']->setItems($addresses);
        $this->presenter['personForm-gravedAddressId']->setItems($addresses);

        $this->presenter->payload->showModal = false;

        $this->presenter->flashMessage('address_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->payload->snippets = [
            $this->presenter['personForm-birthAddressId']->getHtmlId() => (string) $this->presenter['personForm-birthAddressId']->getControl(),
            $this->presenter['personForm-deathAddressId']->getHtmlId() => (string) $this->presenter['personForm-deathAddressId']->getControl(),
            $this->presenter['personForm-gravedAddressId']->getHtmlId() => (string) $this->presenter['personForm-gravedAddressId']->getControl(),
        ];

        $this->presenter->redrawControl('flashes');
        $this->presenter->redrawControl('jsFormCallback');
    }
}
