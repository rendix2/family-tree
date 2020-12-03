<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddGenusModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 23:02
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\GenusForm;

/**
 * Trait PersonAddGenusModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddGenusModal
{
    /**
     * @return void
     */
    public function handlePersonAddGenus()
    {
        $this->template->modalName = 'personAddGenus';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddGenusForm()
    {
        $formFactory = new GenusForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'personAddGenusFormAnchor'];
        $form->onValidate[] = [$this, 'personAddGenusFormValidate'];
        $form->onSuccess[] = [$this, 'personAddGenusFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddGenusFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddGenusFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddGenusFormSuccess(Form $form, ArrayHash $values)
    {
        $this->genusManager->add($values);

        $genuses = $this->genusManager->getPairs('surname');

        $this['form-genusId']->setItems($genuses);

        $this->payload->showModal = false;

        $this->flashMessage('genus_added', self::FLASH_SUCCESS);

        $this->redrawControl('formWrapper');
        $this->redrawControl('flashes');
    }
}
