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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\PersonSelectForm;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
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
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * @var PersonSelectForm $personSelectForm,
     */
    private $personSelectForm;

    /**
     * PersonAddBrotherModal constructor.
     *
     * @param PersonFacade        $personFacade
     * @param PersonFilter        $personFilter
     * @param PersonManager       $personManager
     * @param PersonSelectForm    $personSelectForm
     * @param PersonUpdateService $personUpdateServiceCached
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonSelectForm $personSelectForm,
        PersonUpdateService $personUpdateServiceCached
    ) {
        parent::__construct();

        $this->personSelectForm = $personSelectForm;

        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateServiceCached;
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

        $persons = $this->personManager->select()->getSettingsManager()->getMalesPairs();

        $this['personAddBrotherForm-selectedPersonId']->setItems($persons);
        $this['personAddBrotherForm']->setDefaults(['personId' => $personId,]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);

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
        $persons = $this->personManager->select()->getManager()->getMalesPairs();

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

        $presenter->flashMessage('person_brother_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('brothers');
    }
}
