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
    public function handlePersonAddSon($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {
            $persons = $this->personManager->getMalesPairs($this->getTranslator());

            $this['personAddSonForm-selectedPersonId']->setItems($persons);
            $this['personAddSonForm']->setDefaults(['personId' => $personId,]);

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'personAddSon';
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddSonForm()
    {
        $formFactory = new PersonSelectForm($this->getTranslator());

        $form = $formFactory->create();
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
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddSonFormValidate(Form $form, ArrayHash $values)
    {
        $persons = $this->personManager->getMalesPairs($this->getTranslator());

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
