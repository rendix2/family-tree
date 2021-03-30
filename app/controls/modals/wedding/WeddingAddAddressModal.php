<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingAddAddressModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:25
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding;

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
 * Class WeddingAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingAddAddressModal extends Control
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
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * WeddingAddAddressModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param TownSettingsManager $townSettingsManager
     * @param TownManager $townManager
     * @param ITranslator $translator
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        CountryManager $countryManager,
        TownSettingsManager $townSettingsManager,
        TownManager $townManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressManager = $addressManager;
        $this->countryManager = $countryManager;
        $this->townSettingsManager = $townSettingsManager;
        $this->townManager = $townManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['weddingAddAddressForm']->render();
    }

    /**
     * @return void
     */
    public function handleWeddingAddAddress()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $this['weddingAddAddressForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'weddingAddAddress';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
        $presenter->redrawControl('js');
    }

    /**
     * @param int $countryId countryId
     * @param string $formData
     */
    public function handleWeddingAddAddressSelectCountry($countryId, $formData)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:edit', $presenter->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId']);

        $countries = $this->countryManager->getPairs('name');

        if ($countryId) {
            $towns = $this->townSettingsManager->getPairsByCountry($countryId);

            $this['weddingAddAddressForm-townId']->setItems($towns);
            $this['weddingAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);
        } else {
            $this['weddingAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['weddingAddAddressForm-townId']->setItems([]);
        }

        $this['weddingAddAddressForm']->setDefaults($formDataParsed);

        $presenter->payload->snippets = [
            $this['weddingAddAddressForm-townId']->getHtmlId() => (string) $this['weddingAddAddressForm-townId']->getControl(),
        ];

        $presenter->redrawControl('jsFormCallback');
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('weddingAddAddressSelectCountry!');

        $formFactory = new AddressForm($this->translator, $addressSettings);

        $form = $formFactory->create();
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'weddingAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'weddingAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function weddingAddAddressFormValidate(Form $form)
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
    public function weddingAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:edit', $presenter->getParameter('id'));
        }

        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getPairsCached();

        $presenter['weddingForm-addressId']->setItems($addresses);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->snippets = [
            $presenter['weddingForm-addressId']->getHtmlId() => (string) $presenter['weddingForm-addressId']->getControl(),
        ];

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jsFormCallback');
    }
}
