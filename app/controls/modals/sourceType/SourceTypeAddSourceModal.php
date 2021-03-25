<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeAddSourceModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:32
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\SourceForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class SourceTypeAddSourceModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType
 */
class SourceTypeAddSourceModal extends \Nette\Application\UI\Control
{
    /**
     * @param int $sourceTypeId
     *
     * @return void
     */
    public function handleSourceTypeAddSource($sourceTypeId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $this->redirect('SourceType:edit', $sourceTypeId);
        }

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['sourceTypeAddSourceForm-personId']->setItems($persons);

        $this['sourceTypeAddSourceForm-_sourceTypeId']->setDefaultValue($sourceTypeId);
        $this['sourceTypeAddSourceForm-sourceTypeId']->setItems($sourceTypes)
            ->setDisabled()
            ->setDefaultValue($sourceTypeId);

        $this->template->modalName = 'sourceTypeAddSource';

        $presenter->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeAddSourceForm()
    {
        $formFactory = new SourceForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_sourceTypeId');
        $form->onAnchor[] = [$this, 'sourceTypeAddSourceFormAnchor'];
        $form->onValidate[] = [$this, 'sourceTypeAddSourceFormValidate'];
        $form->onSuccess[] = [$this, 'sourceTypeAddSourceFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function sourceTypeAddSourceFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function sourceTypeAddSourceFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairsCached($this->translator);

        $personControl = $form->getComponent('personId');

        $personControl->setItems($persons);
        $personControl->validate();

        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $sourceTypeHiddenControl = $form->getComponent('_sourceTypeId');

        $sourceTypeControl = $form->getComponent('sourceTypeId');
        $sourceTypeControl->setItems($sourceTypes)
            ->setValue($sourceTypeHiddenControl->getValue())
            ->validate();

        $form->removeComponent($sourceTypeHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function sourceTypeAddSourceFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->sourceManager->add($values);

        $sources = $this->sourceFacade->getBySourceTypeCached($values->sourceTypeId);

        $this->template->souces = $sources;

        $presenter->payload->showModal = false;

        $this->flashMessage('source_added', BasePresenter::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('sources');
    }
}
