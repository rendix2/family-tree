<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSisterModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:05
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonAddSisterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddSisterModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * PersonAddSisterModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonFilter $personFilter
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        PersonFilter $personFilter,
        PersonFacade $personFacade,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->personFilter = $personFilter;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddSisterForm']->render();
    }

    /**
     * @param int $personId
     */
    public function handlePersonAddSister($personId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->presenter->isAjax()) {
            $persons = $this->personSettingsManager->getFemalesPairs($this->translator);

            $this['personAddSisterForm-selectedPersonId']->setItems($persons);
            $this['personAddSisterForm']->setDefaults(['personId' => $personId,]);

            $personFilter = $this->personFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->presenter->template->modalName = 'personAddSister';
            $this->presenter->template->personModalItem = $personFilter($personModalItem);

            $this->presenter->payload->showModal = true;

            $this->presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddSisterForm()
    {
        $formFactory = new PersonSelectForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'personAddSisterFormAnchor'];
        $form->onValidate[] = [$this, 'personAddSisterFormValidate'];
        $form->onSuccess[] = [$this, 'personAddSisterFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddSisterFormAnchor()
    {
        $presenter = $this->presenter;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddSisterFormValidate(Form $form)
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
    public function personAddSisterFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $formData = $form->getHttpData();
        $personId = $this->getParameter('id');
        $selectedPersonId = $formData['selectedPersonId'];

        if ($this->presenter->isAjax()) {
            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $this->personManager->updateByPrimaryKey($selectedPersonId,
                [
                    'fatherId' => $person->father->id,
                    'motherId' => $person->mother->id
                ]
            );

            $this->prepareBrothersAndSisters($person->id, $person->father, $person->mother);

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('person_sister_added', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('sisters');
        } else {
            $this->presenter->redirect('Person:edit', $personId);
        }
    }
}
