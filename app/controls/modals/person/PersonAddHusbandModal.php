<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddHusbandModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:57
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
 * Class PersonAddHusbandModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddHusbandModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var TownSettingsManager  $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * PersonAddHusbandModal constructor.
     * @param ITranslator $translator
     * @param PersonFacade $personFacade
     * @param WeddingManager $weddingManager
     * @param AddressFacade $addressFacade
     * @param TownManager $townManager
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonFacade $personFacade,
        ITranslator $translator,
        PersonManager $personManager,
        TownManager $townManager,
        WeddingManager $weddingManager,
        PersonSettingsManager $personSettingsManager,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personFacade = $personFacade;
        $this->weddingManager = $weddingManager;
        $this->addressFacade = $addressFacade;
        $this->townManager = $townManager;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->townSettingsManager = $townSettingsManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddHusbandForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddHusband($personId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $males = $this->personSettingsManager->getMalesPairs($this->translator);
        $females = $this->personSettingsManager->getFemalesPairs($this->translator);
        $towns = $this->townSettingsManager->getAllPairs();

        $this['personAddHusbandForm-husbandId']->setItems($males);
        $this['personAddHusbandForm-_wifeId']->setDefaultValue($personId);
        $this['personAddHusbandForm-wifeId']->setItems($females)
            ->setDisabled()
            ->setDefaultValue($personId);
        $this['personAddHusbandForm-townId']->setItems($towns);

        $this->presenter->template->modalName = 'personAddHusband';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
        $this->presenter->redrawControl('js');
    }

    /**
     * @param int $townId
     * @param string $formData
     */
    public function handlePersonAddHusbandFormSelectTown($townId, $formData)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['addressId'], $formDataParsed['husbandId']);

        $towns = $this->townSettingsManager->getAllPairs();

        if ($townId) {
            $addresses = $this->addressFacade->getByTownPairs($townId);

            $this['personAddHusbandForm-addressId']->setItems($addresses);
            $this['personAddHusbandForm-townId']->setItems($towns)->setDefaultValue($townId);
        } else {
            $this['personAddHusbandForm-addressId']->setItems([]);
            $this['personAddHusbandForm-townId']->setItems($towns)->setDefaultValue(null);
        }

        $this['personAddHusbandForm']->setDefaults($formDataParsed);

        $this->presenter->payload->snippets = [
            $this['personAddHusbandForm-addressId']->getHtmlId() => (string) $this['personAddHusbandForm-addressId']->getControl(),
        ];

        $this->presenter->redrawControl('jsFormCallback');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddHusbandForm()
    {
        $weddingSettings = new WeddingSettings();
        $weddingSettings->selectTownHandle = $this->link('personAddHusbandFormSelectTown!');

        $formFactory = new WeddingForm($this->translator, $weddingSettings);

        $form = $formFactory->create();
        $form->addHidden('_wifeId');
        $form->onValidate[] = [$this, 'personAddHusbandFormValidate'];
        $form->onSuccess[] = [$this, 'personAddHusbandFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function personAddHusbandFormValidate(Form $form)
    {
        $males = $this->personManager->getMalesPairs($this->translator);

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($males)
            ->validate();

        $females = $this->personManager->getFemalesPairs($this->translator);

        $wifeHiddenControl = $form->getComponent('_wifeId');

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($females);
        $wifeControl->setValue($wifeHiddenControl->getValue())
            ->validate();

        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->getAllPairs();

        $townControl = $form->getComponent('addressId');
        $townControl->setItems($addresses)
            ->validate();

        $form->removeComponent($wifeHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddHusbandFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->weddingManager->add($values);

        $person = $this->personFacade->getByPrimaryKeyCached($this->getParameter('id'));

        $this->prepareWeddings($values->wifeId);
        $this->prepareParentsWeddings($person->father, $person->mother);

        $this->presenter->payload->showModal = false;

        $this->presenter->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->redrawControl('flashes');
        $this->presenter->redrawControl('husbands');
        $this->presenter->redrawControl('mother_husbands');
        $this->presenter->redrawControl('jsFormCallback');
    }
}
