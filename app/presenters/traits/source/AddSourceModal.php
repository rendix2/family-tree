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
    public function handleAddSource()
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['addSourceForm-personId']->setItems($persons);
        $this['addSourceForm-sourceTypeId']->setItems($sourceTypes);

        $this->template->modalName = 'addSource';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddSourceForm()
    {
        $formFactory = new SourceForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addSourceFormAnchor'];
        $form->onValidate[] = [$this, 'addSourceFormValidate'];
        $form->onSuccess[] = [$this, 'saveSourceForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addSourceFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addSourceFormValidate(Form $form)
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
    public function saveSourceForm(Form $form, ArrayHash $values)
    {
        $this->sourceManager->add($values);

        $this->flashMessage('source_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}