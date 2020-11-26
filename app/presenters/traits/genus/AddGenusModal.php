<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddGenusModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 21:07
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Genus;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\GenusForm;

/**
 * Trait AddGenusModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
trait AddGenusModal
{
    /**
     * @return void
     */
    public function handleAddGenus()
    {
        $this->template->modalName = 'addGenus';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddGenusForm()
    {
        $formFactory = new GenusForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addGenusFormAnchor'];
        $form->onValidate[] = [$this, 'addGenusFormValidate'];
        $form->onSuccess[] = [$this, 'saveGenusForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addGenusFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addGenusFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveGenusForm(Form $form, ArrayHash $values)
    {
        $this->genusManager->add($values);

        $this->flashMessage('genus_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}
