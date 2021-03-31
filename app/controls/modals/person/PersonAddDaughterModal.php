<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddDaughterModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:54
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\PersonSelectForm;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddDaughterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddDaughterModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonSelectForm $personSelectForm
     */
    private $personSelectForm;

    /**
     * PersonAddDaughterModal constructor.
     *
     * @param ITranslator           $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonManager         $personManager
     * @param PersonFacade          $personFacade
     * @param PersonFilter          $personFilter
     * @param PersonSelectForm      $personSelectForm
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        PersonManager $personManager,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonSelectForm $personSelectForm
    ) {
        parent::__construct();

        $this->personSelectForm = $personSelectForm;

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->personManager = $personManager;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddDaughterForm']->render();
    }

    /**
     * @param int $personId
     */
    public function handlePersonAddDaughter($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getFemalesPairs($this->translator);

        $this['personAddDaughterForm-selectedPersonId']->setItems($persons);
        $this['personAddDaughterForm']->setDefaults(['personId' => $personId,]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'personAddDaughter';
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddDaughterForm()
    {
        $form = $this->personSelectForm->create();

        $form->onAnchor[] = [$this, 'personAddDaughterFormAnchor'];
        $form->onValidate[] = [$this, 'personAddDaughterFormValidate'];
        $form->onSuccess[] = [$this, 'personAddDaughterFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddDaughterFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddDaughterFormValidate(Form $form)
    {
        $persons = $this->personManager->getFemalesPairs($this->translator);

        $component = $form->getComponent('selectedPersonId');
        $component->setItems($persons)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddDaughterFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $formData = $form->getHttpData();
        $personId = $values->personId;
        $selectedPersonId = $formData['selectedPersonId'];

        $person = $this->personFacade->getByPrimaryKeyCached($personId);

        if ($person->gender === 'm') {
            $this->personManager->updateByPrimaryKey($selectedPersonId, ['fatherId' => $personId]);
        } else {
            $this->personManager->updateByPrimaryKey($selectedPersonId, ['motherId' => $personId]);
        }

        $daughters = $this->personSettingsManager->getDaughtersByPersonCached($person);

        $presenter->template->daughters = $daughters;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_daughter_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('daughters');
    }
}
