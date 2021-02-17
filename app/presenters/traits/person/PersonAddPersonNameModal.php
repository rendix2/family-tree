<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonNameModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:22
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\NameForm;

/**
 * Trait PersonAddPersonNameModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddPersonNameModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPersonName($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['personAddPersonNameForm-personId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['personAddPersonNameForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonNameForm-genusId']->setItems($genuses);

        $this->template->modalName = 'personAddPersonName';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonNameForm()
    {
        $formFactory = new NameForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_personId');
        $form->onAnchor[] = [$this, 'personAddPersonNameFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPersonNameFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPersonNameFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPersonNameFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonNameFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->translator);

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $genuses = $this->genusManager->getPairsCached('surname');

        $genusControl = $form->getComponent('genusId');
        $genusControl->setItems($genuses)
            ->validate();

        $form->removeComponent($personHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPersonNameFormSuccess(Form $form, ArrayHash $values)
    {
        $this->nameManager->add($values);

        $names = $this->nameFacade->getByPersonCached($values->personId);

        $this->template->names = $names;

        $this->payload->showModal = false;

        $this->flashMessage('name_added', self::FLASH_SUCCESS);

        $this->redrawControl('names');
        $this->redrawControl('flashes');
    }
}
