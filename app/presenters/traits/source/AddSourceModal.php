<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddSourceModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 2:13
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Source;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\SourceForm;

/**
 * Class AddSourceModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Source
 */
trait AddSourceModal
{
    /**
     * @return void
     */
    public function handleSourceAddSource()
    {
        $persons = $this->personSettingsManager->getAllPairsCached($this->getTranslator());
        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['sourceAddSourceForm-personId']->setItems($persons);
        $this['sourceAddSourceForm-sourceTypeId']->setItems($sourceTypes);

        $this->template->modalName = 'sourceAddSource';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceAddSourceForm()
    {
        $formFactory = new SourceForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'sourceAddSourceFormAnchor'];
        $form->onValidate[] = [$this, 'sourceAddSourceFormValidate'];
        $form->onSuccess[] = [$this, 'sourceAddSourceFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function sourceAddSourceFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function sourceAddSourceFormValidate(Form $form)
    {
        $personControl = $form->getComponent('personId');

        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $personControl->setItems($persons);
        $personControl->validate();

        $sourceTypeControl = $form->getComponent('sourceTypeId');

        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $sourceTypeControl->setItems($sourceTypes);
        $sourceTypeControl->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function sourceAddSourceFormSuccess(Form $form, ArrayHash $values)
    {
        $this->sourceManager->add($values);

        $this->payload->showModal = false;

        $this->flashMessage('source_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
    }
}