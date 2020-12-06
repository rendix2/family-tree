<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceAddSourceTypeModal.php
 * User: Tomáš Babický
 * Date: 04.12.2020
 * Time: 2:50
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Source;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\SourceTypeForm;

/**
 * Trait SourceAddSourceTypeModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Source
 */
trait SourceAddSourceTypeModal
{
    /**
     * @return void
     */
    public function handleSourceAddSourceType()
    {
        $this->template->modalName = 'sourceAddSourceType';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceAddSourceTypeForm()
    {
        $formFactory = new SourceTypeForm($this->getTranslator());

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
        $this->sourceTypeManager->add($values);

        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['sourceForm-sourceTypeId']->setItems($sourceTypes);

        $this->payload->showModal = false;

        $this->flashMessage('source_type_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('sourceFormWrapper');
        $this->redrawControl('js');
    }
}