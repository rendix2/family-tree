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
 * Class PersonAddHusbandModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddHusbandModal extends Control
{
    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var WeddingForm $weddingForm
     */
    private $weddingForm;

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
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * PersonAddHusbandModal constructor.
     *
     * @param AddressFacade       $addressFacade
     * @param PersonFacade        $personFacade
     * @param PersonManager       $personManager
     * @param TownManager         $townManager
     * @param WeddingManager      $weddingManager
     * @param WeddingForm         $weddingForm
     * @param PersonUpdateService $personUpdateService
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonFacade $personFacade,
        PersonManager $personManager,
        TownManager $townManager,
        WeddingManager $weddingManager,
        WeddingForm $weddingForm,
        PersonUpdateService $personUpdateService
    ) {
        parent::__construct();

        $this->personFacade = $personFacade;
        $this->weddingForm = $weddingForm;
        $this->weddingManager = $weddingManager;
        $this->addressFacade = $addressFacade;
        $this->townManager = $townManager;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;
    }

    public function __destruct()
    {

        $this->weddingForm = null;
        $this->weddingManager = null;

        $this->townManager = null;
        $this->personManager = null;
        $this->personUpdateService = null;

        $this->addressFacade = null;
        $this->personFacade = null;
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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $males = $this->personManager->select()->getManager()->getMalesPairs();
        $females = $this->personManager->select()->getManager()->getFemalesPairs();
        $towns = $this->townManager->select()->getManager()->getAllPairs();

        $this['personAddHusbandForm-husbandId']->setItems($males);
        $this['personAddHusbandForm-_wifeId']->setDefaultValue($personId);
        $this['personAddHusbandForm-wifeId']->setItems($females)
            ->setDisabled()
            ->setDefaultValue($personId);
        $this['personAddHusbandForm-townId']->setItems($towns);

        $presenter->template->modalName = 'personAddHusband';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
        $presenter->redrawControl('js');
    }

    /**
     * @param int $townId
     * @param string $formData
     */
    public function handlePersonAddHusbandFormSelectTown($townId, $formData)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['addressId'], $formDataParsed['husbandId']);

        $towns = $this->townManager->select()->getSettingsManager()->getAllPairs();

        if ($townId) {
            $addresses = $this->addressFacade->select()->getManager()->getByTownPairs($townId);

            $this['personAddHusbandForm-addressId']->setItems($addresses);
            $this['personAddHusbandForm-townId']->setItems($towns)->setDefaultValue($townId);
        } else {
            $this['personAddHusbandForm-addressId']->setItems([]);
            $this['personAddHusbandForm-townId']->setItems($towns)->setDefaultValue(null);
        }

        $this['personAddHusbandForm']->setDefaults($formDataParsed);

        $presenter->payload->snippets = [
            $this['personAddHusbandForm-addressId']->getHtmlId() => (string) $this['personAddHusbandForm-addressId']->getControl(),
        ];

        $presenter->redrawControl('jsFormCallback');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddHusbandForm()
    {
        $weddingSettings = new WeddingSettings();
        $weddingSettings->selectTownHandle = $this->link('personAddHusbandFormSelectTown!');

        $form = $this->weddingForm->create($weddingSettings);

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
        $males = $this->personManager->select()->getManager()->getMalesPairs();

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($males)
            ->validate();

        $females = $this->personManager->select()->getManager()->getFemalesPairs();

        $wifeHiddenControl = $form->getComponent('_wifeId');

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($females);
        $wifeControl->setValue($wifeHiddenControl->getValue())
            ->validate();

        $towns = $this->townManager->select()->getManager()->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->select()->getManager()->getAllPairs();

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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->insert()->insert((array) $values);

        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($presenter->getParameter('id'));

        $this->personUpdateService->prepareWeddings($presenter, $values->wifeId);
        $this->personUpdateService->prepareParentsWeddings($presenter, $person->father, $person->mother);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('husbands');
        $presenter->redrawControl('mother_husbands');
        $presenter->redrawControl('jsFormCallback');
    }
}
