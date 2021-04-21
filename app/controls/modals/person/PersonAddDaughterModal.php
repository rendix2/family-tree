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
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddDaughterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddDaughterModal extends Control
{
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
     * @param ITranslator      $translator
     * @param PersonManager    $personManager
     * @param PersonFacade     $personFacade
     * @param PersonFilter     $personFilter
     * @param PersonSelectForm $personSelectFormCached
     */
    public function __construct(
        PersonManager $personManager,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonSelectForm $personSelectFormCached
    ) {
        parent::__construct();

        $this->personSelectForm = $personSelectFormCached;

        $this->personManager = $personManager;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
    }

    public function __destruct()
    {
        $this->personSelectForm = null;

        $this->personManager = null;

        $this->personFilter = null;

        $this->personFacade = null;
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

        $persons = $this->personManager->select()->getManager()->getFemalesPairs();

        $this['personAddDaughterForm-selectedPersonId']->setItems($persons);
        $this['personAddDaughterForm']->setDefaults(['personId' => $personId,]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);

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
        $persons = $this->personManager->select()->getManager()->getFemalesPairs();

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

        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);

        if ($person->gender === 'm') {
            $this->personManager->update()->updateByPrimaryKey($selectedPersonId, ['fatherId' => $personId]);
        } else {
            $this->personManager->update()->updateByPrimaryKey($selectedPersonId, ['motherId' => $personId]);
        }

        $daughters = $this->personManager->select()->getCachedManager()->getDaughtersByPerson($person);

        $presenter->template->daughters = $daughters;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_daughter_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('daughters');
    }
}
