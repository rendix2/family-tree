<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceTypeFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:34
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\SourceTypeFilter;
use Rendix2\FamilyTree\App\Model\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class SourceTypeDeleteSourceTypeFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType
 */
class SourceTypeDeleteSourceTypeFromListModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * @var SourceTypeFilter $sourceTypeFilter
     */
    private $sourceTypeFilter;

    /**
     * SourceTypeDeleteSourceTypeFromListModal constructor.
     *
     * @param DeleteModalForm   $deleteModalForm
     * @param SourceTypeManager $sourceTypeManager
     * @param SourceTypeFilter  $sourceTypeFilter
     */
    public function __construct(

        DeleteModalForm $deleteModalForm,
        SourceTypeManager $sourceTypeManager,
        SourceTypeFilter $sourceTypeFilter
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->sourceTypeManager = $sourceTypeManager;
        $this->sourceTypeFilter = $sourceTypeFilter;
    }

    public function render()
    {
        $this['sourceTypeDeleteSourceTypeFromListForm']->render();
    }

    /**
     * @param int $sourceTypeId
     */
    public function handleSourceTypeDeleteSourceTypeFromList($sourceTypeId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('SourceType:default');
        }

        $this['sourceTypeDeleteSourceTypeFromListForm']->setDefaults(['sourceTypeId' => $sourceTypeId]);

        $sourceTypeModalItem = $this->sourceTypeManager->select()->getManager()->getByPrimaryKey($sourceTypeId);

        $sourceTypeFilter = $this->sourceTypeFilter;

        $presenter->template->modalName = 'sourceTypeDeleteSourceTypeFromList';
        $presenter->template->sourceTypeModalItem = $sourceTypeFilter($sourceTypeModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeDeleteSourceTypeFromListForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'sourceTypeDeleteSourceTypeFromListFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('sourceTypeId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceTypeDeleteSourceTypeFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('SourceType:default');
        }

        try {
            $this->sourceTypeManager->delete()->deleteByPrimaryKey($values->sourceTypeId);

            $presenter->flashMessage('source_type_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $presenter->redrawControl('flashes');
        }
    }
}
