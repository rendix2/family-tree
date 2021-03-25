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
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

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

        $presenter->template->modalName = 'nameAddGenus';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
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
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
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

        $presenter->payload->showModal = false;

        $presenter->flashMessage('genus_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('nameFormWrapper');
        $presenter->redrawControl('jsFormCallback');
    }
}