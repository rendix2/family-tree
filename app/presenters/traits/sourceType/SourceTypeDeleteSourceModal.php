<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:08
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\SourceType;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait SourceTypeDeleteSourceModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\SourceType
 */
trait SourceTypeDeleteSourceModal
{
    /**
     * @param int $sourceTypeId
     * @param int $sourceId
     */
    public function handleDeleteSourceItem($sourceTypeId, $sourceId)
    {
        if ($this->isAjax()) {
            $this['deleteSourceForm']->setDefaults(
                [
                    'sourceTypeId' => $sourceTypeId,
                    'sourceId' => $sourceId
                ]
            );

            $sourceFilter = new SourceFilter() ;

            $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

            $this->template->modalName = 'deleteSourceItem';
            $this->template->sourceModalItem = $sourceFilter($sourceModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteSourceForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create($this, 'deleteSourceFormOk');
        $form->addHidden('sourceTypeId');
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteSourceFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            try {
                $this->sourceManager->deleteByPrimaryKey($values->sourceId);

                $sources = $this->sourceFacade->getBySourceTypeId($values->sourceTypeId);

                $this->template->sources = $sources;

                $this->payload->showModal = false;

                $this->flashMessage('source_was_deleted', self::FLASH_SUCCESS);

                $this->redrawControl('sources');
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
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
