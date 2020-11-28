<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSonModal.php
 * User: Tomáš Babický
 * Date: 04.11.2020
 * Time: 11:04
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;

/**
 * Trait PersonAddSonModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddSonModal
{
    /**
     * @param int $personId
     */
    public function handleAddSon($personId)
    {
        if ($this->isAjax()) {
            $persons = $this->personManager->getMalesPairs($this->getTranslator());

            $this['addSonForm-selectedPersonId']->setItems($persons);
            $this['addSonForm']->setDefaults(['personId' => $personId,]);

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'addSon';
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddSonForm()
    {
        $formFactory = new PersonSelectForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'addSonFormSuccess'];
        $form->onAnchor[] = [$this, 'addSonFormAnchor'];
        $form->onValidate[] = [$this, 'addSonFormValidate'];

        return $form;
    }

    /**
     * @param Form $form
     *
     * @return void
     */
    public function addSonFormAnchor(Form $form)
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addSonFormValidate(Form $form, ArrayHash $values)
    {
        $persons = $this->personManager->getMalesPairs($this->getTranslator());

        $component = $form->getComponent('selectedPersonId');
        $component->setItems($persons);
        $component->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addSonFormSuccess(Form $form, ArrayHash $values)
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

            $sons = $this->personManager->getSonsByPersonCached($person);

            $this->template->sons = $sons;

            $this->payload->showModal = false;

            $this->flashMessage('person_son_added', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('sons');
        } else {
            $this->redirect('Person:edit', $personId);
        }
    }
}
