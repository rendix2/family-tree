<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:33
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class SourceTypeDeleteSourceModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType
 */
class SourceTypeDeleteSourceModal extends \Nette\Application\UI\Control
{
    /**
     * @param int $sourceTypeId
     * @param int $sourceId
     */
    public function handleSourceTypeDeleteSource($sourceTypeId, $sourceId)
    {
        $presenter = $this->presenter;

        if ($this->isAjax()) {
            $this['sourceTypeDeleteSourceForm']->setDefaults(
                [
                    'sourceTypeId' => $sourceTypeId,
                    'sourceId' => $sourceId
                ]
            );

            $sourceFilter = $this->sourceFilter;

            $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

            $this->template->modalName = 'sourceTypeDeleteSource';
            $this->template->sourceModalItem = $sourceFilter($sourceModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeDeleteSourceForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'sourceTypeDeleteSourceForYesOnClick']);
        $form->addHidden('sourceTypeId');
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceTypeDeleteSourceForYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($this->isAjax()) {
            try {
                $this->sourceManager->deleteByPrimaryKey($values->sourceId);

                $sources = $this->sourceFacade->getBySourceTypeId($values->sourceTypeId);

                $this->template->sources = $sources;

                $this->payload->showModal = false;

                $this->flashMessage('source_deleted', BasePresenter::FLASH_SUCCESS);

                $this->redrawControl('sources');
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
                } else {
                    Debugger::log($e, ILogger::EXCEPTION);
                }
            } finally {
                $this->redrawControl('flashes');
            }
        } else {
            $this->redirect('SourceType:edit', $values->sourceTypeId);
        }
    }
}