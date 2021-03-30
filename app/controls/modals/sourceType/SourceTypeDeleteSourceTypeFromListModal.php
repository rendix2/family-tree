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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Filters\SourceTypeFilter;

use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
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
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * @var SourceTypeFilter $sourceTypeFilter
     */
    private $sourceTypeFilter;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * SourceTypeDeleteSourceTypeFromListModal constructor.
     *
     * @param SourceTypeManager $sourceTypeManager
     * @param SourceTypeFilter $sourceTypeFilter
     * @param ITranslator $translator
     */
    public function __construct(

        DeleteModalForm $deleteModalForm,

        SourceTypeManager $sourceTypeManager,
        SourceTypeFilter $sourceTypeFilter,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->sourceTypeManager = $sourceTypeManager;
        $this->sourceTypeFilter = $sourceTypeFilter;
        $this->translator = $translator;
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

        $sourceTypeModalItem = $this->sourceTypeManager->getByPrimaryKeyCached($sourceTypeId);

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
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'sourceTypeDeleteSourceTypeFromListFormYesOnClick']);
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
            $this->sourceTypeManager->deleteByPrimaryKey($values->sourceTypeId);

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
