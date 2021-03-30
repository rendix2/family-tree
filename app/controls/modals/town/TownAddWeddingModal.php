<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddModalWedding.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Controls\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;


use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownAddWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownAddWeddingModal extends Control
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
     * @var ITranslator $translator
     */
    private $translator;

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
     * @var WeddingForm $weddingForm
     */
    private $weddingForm;

    /**
     * TownAddWeddingModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param ITranslator $translator
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        ITranslator $translator,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager,
        WeddingFacade $weddingFacade,
        WeddingForm $weddingForm,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->translator = $translator;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
        $this->weddingFacade = $weddingFacade;
        $this->weddingForm = $weddingForm;
        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['townAddWeddingForm']->render();
    }

    /**
     * @param int $townId
     *
     * @return void
     */
    public function handleTownAddWedding($townId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $males = $this->personSettingsManager->getMalesPairs($this->translator);
        $females = $this->personSettingsManager->getFemalesPairs($this->translator);
        $towns = $this->townSettingsManager->getAllPairs();
        $addresses = $this->addressFacade->getByTownPairs($townId);

        $this['townAddWeddingForm-husbandId']->setItems($males);
        $this['townAddWeddingForm-wifeId']->setItems($females);
        $this['townAddWeddingForm-_townId']->setDefaultValue($townId);
        $this['townAddWeddingForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);
        $this['townAddWeddingForm-addressId']->setItems($addresses);

        $presenter->template->modalName = 'townAddWedding';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddWeddingForm()
    {
        $weddingSettings = new WeddingSettings();

        $form = $this->weddingForm->create($weddingSettings);

        $form->addHidden('_townId');

        $form->onAnchor[] = [$this, 'townAddWeddingFormAnchor'];
        $form->onValidate[] = [$this, 'townAddWeddingFormValidate'];
        $form->onSuccess[] = [$this, 'townAddWeddingFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function townAddWeddingFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function townAddWeddingFormValidate(Form $form)
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

        $addresses = $this->addressFacade->getByTownPairs($townHiddenControl->getValue());

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();

        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townAddWeddingFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->add($values);

        $weddings = $this->weddingFacade->getByTownIdCached($values->townId);

        $presenter->template->weddings = $weddings;

        $presenter->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->showModal = false;

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('weddings');
    }
}
