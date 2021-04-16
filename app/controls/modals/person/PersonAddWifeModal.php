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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Helpers\FormJsonDataParser;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Controls\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;


/**
 * Class PersonAddWifeModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddWifeModal extends Control
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var WeddingForm $weddingForm
     */
    private $weddingForm;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * PersonAddWifeModal constructor.
     *
     * @param AddressFacade       $addressFacade
     * @param PersonFacade        $personFacade
     * @param PersonManager       $personManager
     * @param PersonUpdateService $personUpdateService
     * @param TownManager         $townManager
     * @param WeddingForm         $weddingForm
     * @param WeddingManager      $weddingManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonFacade $personFacade,
        PersonManager $personManager,
        PersonUpdateService $personUpdateService,
        TownManager $townManager,
        WeddingForm $weddingForm,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;
        $this->townManager = $townManager;
        $this->weddingForm = $weddingForm;
        $this->weddingManager = $weddingManager;
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

        $males = $this->personManager->select()->getSettingsManager()->getMalesPairs();
        $females = $this->personManager->select()->getSettingsManager()->getFemalesPairs();
        $towns = $this->townManager->select()->getSettingsManager()->getAllPairs();

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

        $towns = $this->townManager->select()->getSettingsManager()->getAllPairs();

        if ($townId) {
            $addresses = $this->addressFacade->select()->getManager()->getByTownPairs($townId);

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

        $form = $this->weddingForm->create($weddingSettings);

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
        $persons = $this->personManager->select()->getManager()->getMalesPairs();

        $husbandHiddenControl = $form->getComponent('_husbandId');

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->setValue($husbandHiddenControl->getValue())
            ->validate();

        $persons = $this->personManager->select()->getManager()->getFemalesPairs();

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons)
            ->validate();

        $towns = $this->townManager->select()->getManager()->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->select()->getManager()->getAllPairs();

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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->insert()->insert((array) $values);

        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($presenter->getParameter('id'));

        $this->personUpdateService->prepareWeddings($presenter, $values->husbandId);
        $this->personUpdateService->prepareParentsWeddings($presenter, $person->father, $person->mother);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jsFormCallback');
        $presenter->redrawControl('father_wives');
        $presenter->redrawControl('wives');
    }
}
