<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddWifeModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:07
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonAddWifeModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddWifeModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * PersonAddWifeModal constructor.
     *
     * @param PersonSettingsManager $personSettingsManager
     * @param TownSettingsManager $townSettingsManager
     * @param ITranslator $translator
     * @param AddressFacade $addressFacade
     * @param PersonManager $personManager
     * @param TownManager $townManager
     * @param WeddingManager $weddingManager
     * @param PersonFacade $personFacade
     */
    public function __construct(
        PersonSettingsManager $personSettingsManager,
        TownSettingsManager $townSettingsManager,
        ITranslator $translator,
        AddressFacade $addressFacade,
        PersonManager $personManager,
        TownManager $townManager,
        WeddingManager $weddingManager,
        PersonFacade $personFacade
    ) {
        parent::__construct();

        $this->personSettingsManager = $personSettingsManager;
        $this->townSettingsManager = $townSettingsManager;
        $this->translator = $translator;
        $this->addressFacade = $addressFacade;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
        $this->weddingManager = $weddingManager;
        $this->personFacade = $personFacade;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddWifeForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddWife($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $males = $this->personSettingsManager->getMalesPairs($this->translator);
        $females = $this->personSettingsManager->getFemalesPairs($this->translator);
        $towns = $this->townSettingsManager->getAllPairs();

        $this['personAddWifeForm-_husbandId']->setDefaultValue($personId);
        $this['personAddWifeForm-husbandId']->setItems($males)->setDisabled()->setDefaultValue($personId);
        $this['personAddWifeForm-wifeId']->setItems($females);
        $this['personAddWifeForm-townId']->setItems($towns);

        $presenter->template->modalName = 'personAddWife';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
        $presenter->redrawControl('js');
    }

    /**
     * @param int $townId
     * @param string $formData
     */
    public function handlePersonAddWifeFormSelectTown($townId, $formData)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['addressId'], $formDataParsed['wifeId']);

        $towns = $this->townSettingsManager->getAllPairs();

        if ($townId) {
            $addresses = $this->addressFacade->getByTownPairs($townId);

            $this['personAddWifeForm-addressId']->setItems($addresses);
            $this['personAddWifeForm-townId']->setItems($towns)
                ->setDefaultValue($townId);
        } else {
            $this['personAddWifeForm-addressId']->setItems([]);
            $this['personAddWifeForm-townId']->setItems($towns)
                ->setDefaultValue(null);
        }

        $this['personAddWifeForm']->setDefaults($formDataParsed);

        $presenter->payload->snippets = [
            $this['personAddWifeForm-addressId']->getHtmlId() => (string) $this['personAddWifeForm-addressId']->getControl(),
        ];

        $presenter->redrawControl('jsFormCallback');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddWifeForm()
    {
        $weddingSettings = new WeddingSettings();
        $weddingSettings->selectTownHandle = $this->link('personAddWifeFormSelectTown!');

        $formFactory = new WeddingForm($this->translator, $weddingSettings);

        $form = $formFactory->create();
        $form->addHidden('_husbandId');
        $form->onValidate[] = [$this, 'personAddWifeFormValidate'];
        $form->onSuccess[] = [$this, 'personAddWifeFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function personAddWifeFormValidate(Form $form)
    {
        $persons = $this->personManager->getMalesPairs($this->translator);

        $husbandHiddenControl = $form->getComponent('_husbandId');

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->setValue($husbandHiddenControl->getValue())
            ->validate();

        $persons = $this->personManager->getFemalesPairs($this->translator);

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons)
            ->validate();

        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->getAllPairs();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();

        $form->removeComponent($husbandHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddWifeFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->weddingManager->add($values);

        $person = $this->personFacade->getByPrimaryKeyCached($presenter->getParameter('id'));

        $this->prepareWeddings($values->husbandId);
        $this->prepareParentsWeddings($person->father, $person->mother);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jsFormCallback');
        $presenter->redrawControl('father_wives');
        $presenter->redrawControl('wives');
    }
}
