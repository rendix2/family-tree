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
    public function handleAddSourceType()
    {
        $this->template->modalName = 'addSourceType';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddSourceTypeForm()
    {
        $formFactory = new SourceTypeForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addSourceTypeFormAnchor'];
        $form->onValidate[] = [$this, 'addSourceTypeFormValidate'];
        $form->onSuccess[] = [$this, 'saveSourceTypeForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addSourceTypeFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addSourceTypeFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveSourceTypeForm(Form $form, ArrayHash $values)
    {
        $this->sourceTypeManager->add($values);

        $this->flashMessage('source_type_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}