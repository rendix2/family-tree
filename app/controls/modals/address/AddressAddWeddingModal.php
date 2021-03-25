<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddModalWedding.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddWeddingModal extends Control
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

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
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * AddressAddWeddingModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     * @param ITranslator $translator
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager,
        ITranslator $translator,
        WeddingFacade $weddingFacade,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
        $this->translator = $translator;
        $this->weddingFacade = $weddingFacade;
        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['addressAddWeddingForm']->render();
    }

    /**
     * @param int $townId
     * @param int $addressId
     *
     * @return void
     */
    public function handleAddressAddWedding($townId, $addressId)
    {
        $presenter = $this->presenter;

        $males = $this->personSettingsManager->getMalesPairs($this->translator);
        $females = $this->personSettingsManager->getFemalesPairs($this->translator);
        $towns = $this->townSettingsManager->getAllPairs();
        $addresses = $this->addressFacade->getAllPairs();

        $this['addressAddWeddingForm-husbandId']->setItems($males);
        $this['addressAddWeddingForm-wifeId']->setItems($females);
        $this['addressAddWeddingForm-_townId']->setDefaultValue($townId);
        $this['addressAddWeddingForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);

        $this['addressAddWeddingForm-_addressId']->setDefaultValue($addressId);
        $this['addressAddWeddingForm-addressId']->setItems($addresses)
            ->setDisabled()
            ->setDefaultValue($addressId);

        $presenter->template->modalName = 'addressAddWedding';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddWeddingForm()
    {
        $weddingSettings = new WeddingSettings();

        $formFactory = new WeddingForm($this->translator, $weddingSettings);

        $form = $formFactory->create();
        $form->addHidden('_addressId');
        $form->addHidden('_townId');
        $form->onAnchor[] = [$this, 'addressAddWeddingFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddWeddingFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddWeddingFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddWeddingFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addressAddWeddingFormValidate(Form $form)
    {
        $persons = $this->personManager->getMalesPairs($this->translator);

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->validate();

        $persons = $this->personManager->getFemalesPairs($this->translator);

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons)
            ->validate();

        $towns = $this->townManager->getAllPairs();

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->getAllPairs();

        $addressHiddenControl = $form->getComponent('_addressId');

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->setValue($addressHiddenControl->getValue())
            ->validate();

        $form->removeComponent($addressHiddenControl);
        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddWeddingFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->weddingManager->add($values);

        $weddings = $this->weddingFacade->getByTownIdCached($values->townId);

        $presenter->template->weddings = $weddings;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('weddings');
    }
}
