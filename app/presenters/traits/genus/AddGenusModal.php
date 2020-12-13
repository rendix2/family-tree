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
    public function handleGenusAddGenus()
    {
        $this->template->modalName = 'genusAddGenus';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentGenusAddGenusForm()
    {
        $formFactory = new GenusForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'genusAddGenusFormAnchor'];
        $form->onValidate[] = [$this, 'genusAddGenusFormValidate'];
        $form->onSuccess[] = [$this, 'genusAddGenusFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function genusAddGenusFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function genusAddGenusFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function genusAddGenusFormSuccess(Form $form, ArrayHash $values)
    {
        $this->genusManager->add($values);

        $this->payload->showModal = false;

        $this->flashMessage('genus_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
    }
}
