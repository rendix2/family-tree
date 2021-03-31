<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddBrotherModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:53
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
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonAddBrotherModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddBrotherModal extends Control
{
    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * @var PersonSelectForm $personSelectForm,
     */
    private $personSelectForm;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * PersonAddBrotherModal constructor.
     *
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonUpdateService $personUpdateService
     * @param ITranslator $translator
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonSelectForm $personSelectForm,
        PersonSettingsManager $personSettingsManager,
        PersonUpdateService $personUpdateService,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->personSelectForm = $personSelectForm;

        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->personUpdateService = $personUpdateService;
        $this->translator = $translator;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddBrotherForm']->render();
    }

    /**
     * @param int $personId
     */
    public function handlePersonAddBrother($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getMalesPairs($this->translator);

        $this['personAddBrotherForm-selectedPersonId']->setItems($persons);
        $this['personAddBrotherForm']->setDefaults(['personId' => $personId,]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'personAddBrother';
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddBrotherForm()
    {
        $form = $this->personSelectForm->create();

        $form->onAnchor[] = [$this, 'personAddBrotherFormAnchor'];
        $form->onValidate[] = [$this, 'personAddBrotherFormValidate'];
        $form->onSuccess[] = [$this, 'personAddBrotherFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     *
     * @return void
     */
    public function personAddBrotherFormAnchor(Form $form)
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddBrotherFormValidate(Form $form, ArrayHash $values)
    {
        $persons = $this->personManager->getMalesPairs($this->translator);

        $component = $form->getComponent('selectedPersonId');
        $component->setItems($persons)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddBrotherFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $formData = $form->getHttpData();
        $selectedPersonId = $formData['selectedPersonId'];

        $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

        $this->personManager->updateByPrimaryKey($selectedPersonId,
            [
                'fatherId' => $person->father->id,
                'motherId' => $person->mother->id
            ]
        );

        $this->personUpdateService->prepareBrothersAndSisters(
            $presenter,
            $person->id,
            $person->father,
            $person->mother
        );

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_brother_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('brothers');
    }
}
