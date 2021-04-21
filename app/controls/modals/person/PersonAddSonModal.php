<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSonModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:06
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

/**
 * Class PersonAddSonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddSonModal extends Control
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
     * PersonAddSonModal constructor.
     *
     * @param PersonFilter     $personFilter
     * @param PersonFacade     $personFacade
     * @param PersonManager    $personManager
     * @param PersonSelectForm $personSelectFormCached
     */
    public function __construct(
        PersonFilter $personFilter,
        PersonFacade $personFacade,
        PersonManager $personManager,
        PersonSelectForm $personSelectFormCached
    ) {
        parent::__construct();

        $this->personSelectForm = $personSelectFormCached;

        $this->personFilter = $personFilter;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
    }

    public function __destruct()
    {
        $this->personSelectForm = null;

        $this->personFilter = null;

        $this->personManager = null;

        $this->personFacade = null;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddSonForm']->render();
    }

    /**
     * @param int $personId
     */
    public function handlePersonAddSon($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personManager->select()->getManager()->getMalesPairs();

        $this['personAddSonForm-selectedPersonId']->setItems($persons);
        $this['personAddSonForm']->setDefaults(['personId' => $personId,]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);

        $presenter->template->modalName = 'personAddSon';
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddSonForm()
    {
        $form = $this->personSelectForm->create();

        $form->onSuccess[] = [$this, 'personAddSonFormSuccess'];
        $form->onAnchor[] = [$this, 'personAddSonFormAnchor'];
        $form->onValidate[] = [$this, 'personAddSonFormValidate'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     *
     * @return void
     */
    public function personAddSonFormAnchor(Form $form)
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddSonFormValidate(Form $form, ArrayHash $values)
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
    public function personAddSonFormSuccess(Form $form, ArrayHash $values)
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

        $sons = $this->personManager->select()->getSettingsCachedManager()->getSonsByPerson($person);

        $presenter->template->sons = $sons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_son_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sons');
    }
}
