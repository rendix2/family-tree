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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\PersonSelectForm;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonAddSisterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddSisterModal extends Control
{
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
     * @var PersonSelectForm $personSelectForm
     */
    private $personSelectForm;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * PersonAddSisterModal constructor.
     *
     * @param PersonFacade        $personFacade
     * @param PersonFilter        $personFilter
     * @param PersonManager       $personManager
     * @param PersonSelectForm    $personSelectFormCached
     * @param PersonUpdateService $personUpdateService
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonSelectForm $personSelectFormCached,
        PersonUpdateService $personUpdateService
    ) {
        parent::__construct();

        $this->personSelectForm = $personSelectFormCached;

        $this->personFilter = $personFilter;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;
    }

    public function __destruct()
    {
        $this->personSelectForm = null;

        $this->personFilter = null;

        $this->personManager = null;
        $this->personUpdateService = null;

        $this->personFacade = null;
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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personManager->select()->getManager()->getFemalesPairs();

        $this['personAddSisterForm-selectedPersonId']->setItems($persons);
        $this['personAddSisterForm']->setDefaults(['personId' => $personId,]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);

        $presenter->template->modalName = 'personAddSister';
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddSisterForm()
    {
        $form = $this->personSelectForm->create();

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

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddSisterFormValidate(Form $form)
    {
        $persons = $this->personManager->select()->getManager()->getFemalesPairs();

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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $formData = $form->getHttpData();
        $selectedPersonId = $formData['selectedPersonId'];

        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($values->personId);

        $this->personManager->update()->updateByPrimaryKey($selectedPersonId,
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

        $presenter->flashMessage('person_sister_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sisters');
    }
}
