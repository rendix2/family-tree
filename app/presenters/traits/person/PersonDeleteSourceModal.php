<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSourceModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 2:23
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteSourceModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteSourceModal
{
    /**
     * @param int $personId
     * @param int $sourceId
     */
    public function handlePersonDeleteSource($personId, $sourceId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {
            $this['personDeleteSourceForm']->setDefaults(
                [
                    'personId' => $personId,
                    'sourceId' => $sourceId
                ]
            );

            $personFilter = new PersonFilter($this->translator, $this->getHttpRequest());
            $sourceFilter = new SourceFilter();

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

            $this->template->modalName = 'personDeleteSource';
            $this->template->sourceModalItem = $sourceFilter($sourceModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteSourceForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteSourceFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteSourceFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $sources = $this->sourceFacade->getByPersonId($values->personId);

            $this->template->sources = $sources;

            $this->payload->showModal = false;

            $this->flashMessage('source_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('sources');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
