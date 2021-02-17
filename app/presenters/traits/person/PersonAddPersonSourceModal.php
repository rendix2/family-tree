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
     *
     * @return void
     */
    public function handlePersonAddPersonSource($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['personAddPersonSourceForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonSourceForm-personId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['personAddPersonSourceForm-sourceTypeId']->setItems($sourceTypes);

        $this->template->modalName = 'personAddPersonSource';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonSourceForm()
    {
        $formFactory = new SourceForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_personId');
        $form->onAnchor[] = [$this, 'personAddPersonSourceFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPersonSourceFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPersonSourceFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPersonSourceFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonSourceFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairsCached($this->translator);

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $sourceTypeControl = $form->getComponent('sourceTypeId');
        $sourceTypeControl->setItems($sourceTypes)
            ->validate();

        $form->removeComponent($personHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPersonSourceFormSuccess(Form $form, ArrayHash $values)
    {
        $this->sourceManager->add($values);

        $sources = $this->sourceFacade->getByPersonId($values->personId);

        $this->template->sources = $sources;

        $this->payload->showModal = false;

        $this->flashMessage('source_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('sources');
    }
}
