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
    public function handleAddPersonName($personId)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['addPersonNameForm-personId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['addPersonNameForm-_personId']->setDefaultValue($personId);
        $this['addPersonNameForm-genusId']->setItems($genuses);

        $this->template->modalName = 'addPersonName';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddPersonNameForm()
    {
        $formFactory = new NameForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_personId');
        $form->onAnchor[] = [$this, 'addPersonNameFormAnchor'];
        $form->onValidate[] = [$this, 'addPersonNameFormValidate'];
        $form->onSuccess[] = [$this, 'savePersonNameForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addPersonNameFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addPersonNameFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());

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
    public function savePersonNameForm(Form $form, ArrayHash $values)
    {
        $this->nameManager->add($values);

        $names = $this->nameFacade->getByPersonCached($values->personId);

        $this->template->names = $names;

        $this->flashMessage('name_added', self::FLASH_SUCCESS);

        $this->redrawControl('names');
        $this->redrawControl('flashes');
    }
}
