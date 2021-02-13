<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddBrotherModal.php
 * User: Tomáš Babický
 * Date: 05.11.2020
 * Time: 15:18
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;

/**
 * Trait PersonAddBrotherModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddBrotherModal
{
    /**
     * @param int$personId
     */
    public function handlePersonAddBrother($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {
            $persons = $this->personSettingsManager->getMalesPairs($this->translator);

            $this['personAddBrotherForm-selectedPersonId']->setItems($persons);
            $this['personAddBrotherForm']->setDefaults(['personId' => $personId,]);

            $personFilter = new PersonFilter($this->translator, $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'personAddBrother';
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddBrotherForm()
    {
        $formFactory = new PersonSelectForm($this->translator);

        $form = $formFactory->create();
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
        $this->redrawControl('modal');
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
        if ($this->isAjax()) {
            $formData = $form->getHttpData();
            $selectedPersonId = $formData['selectedPersonId'];

            $person = $this->personFacade->getByPrimaryKey($values->personId);

            $this->personManager->updateByPrimaryKey($selectedPersonId,
                [
                    'fatherId' => $person->father->id,
                    'motherId' => $person->mother->id
                ]
            );

            $this->prepareBrothersAndSisters($person->id, $person->father, $person->mother);

            $this->payload->showModal = false;

            $this->flashMessage('person_brother_added', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('brothers');
        } else {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }
    }
}
