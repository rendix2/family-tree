<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddSourceTypeModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 2:13
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\SourceType;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\SourceTypeForm;

/**
 * Class AddSourceTypeModal
 * 
 * @package Rendix2\FamilyTree\App\Presenters\Traits\SourceType
 */
trait AddSourceTypeModal
{
    /**
     * @return void
     */
    public function handleSourceTypeAddSourceType()
    {
        $this->template->modalName = 'sourceTypeAddSourceType';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeAddSourceTypeForm()
    {
        $formFactory = new SourceTypeForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'sourceTypeAddSourceTypeFormAnchor'];
        $form->onValidate[] = [$this, 'sourceTypeAddSourceTypeFormValidate'];
        $form->onSuccess[] = [$this, 'sourceTypeAddSourceTypeFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function sourceTypeAddSourceTypeFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function sourceTypeAddSourceTypeFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function sourceTypeAddSourceTypeFormSuccess(Form $form, ArrayHash $values)
    {
        $this->sourceTypeManager->add($values);

        $this->payload->showModal = false;

        $this->flashMessage('source_type_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
    }
}