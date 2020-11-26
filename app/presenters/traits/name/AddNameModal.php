<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddNameModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 2:02
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Name;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\NameForm;

/**
 * Class AddNameModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Name
 */
trait AddNameModal
{
    /**
     * @return void
     */
    public function handleAddName()
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['addNameForm-personId']->setItems($persons);
        $this['addNameForm-genusId']->setItems($genuses);

        $this->template->modalName = 'addName';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddNameForm()
    {
        $formFactory = new NameForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addNameFormAnchor'];
        $form->onValidate[] = [$this, 'addNameFormValidate'];
        $form->onSuccess[] = [$this, 'saveNameForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addNameFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addNameFormValidate(Form $form)
    {
        $personControl = $form->getComponent('personId');

        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $personControl->setItems($persons);
        $personControl->validate();

        $genusControl = $form->getComponent('genusId');

        $genuses = $this->genusManager->getPairsCached('surname');

        $genusControl->setItems($genuses);
        $genusControl->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveNameForm(Form $form, ArrayHash $values)
    {
        $this->nameManager->add($values);

        $this->flashMessage('name_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}
