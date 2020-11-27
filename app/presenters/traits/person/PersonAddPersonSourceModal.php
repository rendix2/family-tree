<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonSourceModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:43
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\SourceForm;

trait PersonAddPersonSourceModal
{
    /**
     * @param int $personId
     * @return void
     */
    public function handleAddPersonSource($personId)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['addPersonSourceForm-_personId']->setDefaultValue($personId);
        $this['addPersonSourceForm-personId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['addPersonSourceForm-sourceTypeId']->setItems($sourceTypes);

        $this->template->modalName = 'addPersonSource';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddPersonSourceForm()
    {
        $formFactory = new SourceForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_personId');
        $form->onAnchor[] = [$this, 'addPersonSourceFormAnchor'];
        $form->onValidate[] = [$this, 'addPersonSourceFormValidate'];
        $form->onSuccess[] = [$this, 'savePersonSourceForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addPersonSourceFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addPersonSourceFormValidate(Form $form)
    {
        $personControl = $form->getComponent('personId');
        $personControlHidden = $form->getComponent('_personId');

        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $personControl->setItems($persons);
        $personControl->setValue($personControlHidden->getValue());
        $personControl->validate();

        $sourceTypeControl = $form->getComponent('sourceTypeId');

        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $sourceTypeControl->setItems($sourceTypes);
        $sourceTypeControl->validate();

        $form->removeComponent($personControlHidden);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function savePersonSourceForm(Form $form, ArrayHash $values)
    {
        $this->sourceManager->add($values);

        $sources = $this->sourceFacade->getByPersonId($values->personId);

        $this->template->sources = $sources;

        $this->flashMessage('source_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('flashes');
        $this->redrawControl('sources');
    }
}