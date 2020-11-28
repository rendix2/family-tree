<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSisterModal.php
 * User: Tomáš Babický
 * Date: 05.11.2020
 * Time: 15:18
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;

trait PersonAddSisterModal
{
    /**
     * @param int $personId
     */
    public function handleAddSister($personId)
    {
        if ($this->isAjax()) {
            $persons = $this->personManager->getFemalesPairs($this->getTranslator());

            $this['addSisterForm-selectedPersonId']->setItems($persons);
            $this['addSisterForm']->setDefaults(['personId' => $personId,]);

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'addSister';
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddSisterForm()
    {
        $formFactory = new PersonSelectForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addSisterFormAnchor'];
        $form->onValidate[] = [$this, 'addSisterFormValidate'];
        $form->onSuccess[] = [$this, 'addSisterFormSuccess'];

        return $form;
    }

    /**
     * @return void
     */
    public function addSisterFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addSisterFormValidate(Form $form)
    {
        $persons = $this->personManager->getFemalesPairs($this->getTranslator());

        $component = $form->getComponent('selectedPersonId');
        $component->setItems($persons);
        $component->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addSisterFormSuccess(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();
        $personId = $this->getParameter('id');
        $selectedPersonId = $formData['selectedPersonId'];

        if ($this->isAjax()) {
            $person = $this->personFacade->getByPrimaryKey($values->personId);

            $this->personManager->updateByPrimaryKey($selectedPersonId,
                [
                    'fatherId' => $person->father->id,
                    'motherId' => $person->mother->id
                ]
            );

            $this->prepareBrothersAndSisters($person->id, $person->father, $person->mother);

            $this->payload->showModal = false;

            $this->flashMessage('person_sister_added', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('sisters');
        } else {
            $this->redirect(':edit', $personId);
        }
    }    
}
