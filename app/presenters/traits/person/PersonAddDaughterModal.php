<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddChildModal.php
 * User: Tomáš Babický
 * Date: 03.11.2020
 * Time: 17:09
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;

/**
 * Trait PersonAddDaughterModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddDaughterModal
{
    /**
     * @param int $personId
     */
    public function handleAddDaughter($personId)
    {
        if ($this->isAjax()) {
            $persons = $this->personManager->getFemalesPairs($this->getTranslator());

            $this['addDaughterForm-selectedPersonId']->setItems($persons);
            $this['addDaughterForm']->setDefaults(['personId' => $personId,]);

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'addDaughter';
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddDaughterForm()
    {
        $formFactory = new PersonSelectForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addDaughterFormAnchor'];
        $form->onValidate[] = [$this, 'addDaughterFormValidate'];
        $form->onSuccess[] = [$this, 'addDaughterFormSuccess'];

        return $form;
    }

    /**
     * @return void
     */
    public function addDaughterFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addDaughterFormValidate(Form $form)
    {
        $persons = $this->personManager->getFemalesPairs($this->getTranslator());

        $component = $form->getComponent('selectedPersonId');
        $component->setItems($persons)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addDaughterFormSuccess(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();
        $personId = $values->personId;
        $selectedPersonId = $formData['selectedPersonId'];

        if ($this->isAjax()) {
            $person = $this->personFacade->getByPrimaryKeyCached($personId);

            if ($person->gender === 'm') {
                $this->personManager->updateByPrimaryKey($selectedPersonId, ['fatherId' => $personId]);
            } else {
                $this->personManager->updateByPrimaryKey($selectedPersonId, ['motherId' => $personId]);
            }

            $daughters = $this->personManager->getDaughtersByPersonCached($person);

            $this->template->daughters = $daughters;

            $this->payload->showModal = false;

            $this->flashMessage('person_daughter_added', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('daughters');
        } else {
            $this->redirect('Person:edit', $personId);
        }
    }
}
