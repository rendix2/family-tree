<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceAddSourceTypeModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:39
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Source;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\SourceTypeForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class SourceAddSourceTypeModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Source
 */
class SourceAddSourceTypeModal extends Control
{
    /**
     * @return void
     */
    public function handleSourceAddSourceType()
    {
        $presenter = $this->presenter;

        $this->template->modalName = 'sourceAddSourceType';

        $presenter->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceAddSourceTypeForm()
    {
        $formFactory = new SourceTypeForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'sourceAddSourceTypeFormAnchor'];
        $form->onValidate[] = [$this, 'sourceAddSourceTypeFormValidate'];
        $form->onSuccess[] = [$this, 'sourceAddSourceTypeFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function sourceAddSourceTypeFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function sourceAddSourceTypeFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function sourceAddSourceTypeFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->sourceTypeManager->add($values);

        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['sourceForm-sourceTypeId']->setItems($sourceTypes);

        $presenter->payload->showModal = false;

        $this->flashMessage('source_type_added', BasePresenter::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('sourceFormWrapper');
        $this->redrawControl('jsFormCallback');
    }
}
