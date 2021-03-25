<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameAddGenusModal.php
 * User: Tomáš Babický
 * Date: 04.12.2020
 * Time: 2:14
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\GenusForm;

/**
 * Class NameAddGenusModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name
 */
class NameAddGenusModal extends Control
{
    /**
     * @return void
     */
    public function handleNameAddGenus()
    {
        $presenter = $this->presenter;

        $this->template->modalName = 'nameAddGenus';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentNameAddGenusForm()
    {
        $formFactory = new GenusForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'nameAddGenusFormAnchor'];
        $form->onValidate[] = [$this, 'nameAddGenusFormValidate'];
        $form->onSuccess[] = [$this, 'nameAddGenusFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function nameAddGenusFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function nameAddGenusFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function nameAddGenusFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->genusManager->add($values);

        $genuses = $this->genusManager->getPairsCached('surname');

        $this['nameForm-genusId']->setItems($genuses);

        $this->payload->showModal = false;

        $this->flashMessage('genus_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('nameFormWrapper');
        $this->redrawControl('jsFormCallback');
    }
}
