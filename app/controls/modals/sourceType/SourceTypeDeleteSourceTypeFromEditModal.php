<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceTypeFromEditModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:33
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\SourceTypeFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class SourceTypeDeleteSourceTypeFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType
 */
class SourceTypeDeleteSourceTypeFromEditModal extends Control
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
     * SourceTypeDeleteSourceTypeFromEditModal constructor.
     *
     * @param SourceTypeManager $sourceTypeManager
     * @param SourceTypeFilter $sourceTypeFilter
     * @param ITranslator $translator
     */
    public function __construct(
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
        $this['sourceTypeDeleteSourceTypeFromEditForm']->render();
    }

    /**
     * @param int $sourceTypeId
     */
    public function handleSourceTypeDeleteSourceTypeFromEdit($sourceTypeId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('SourceType:edit', $presenter->getParameter('id'));
        }

        $this['sourceTypeDeleteSourceTypeFromEditForm']->setDefaults(['sourceTypeId' => $sourceTypeId]);

        $sourceTypeModalItem = $this->sourceTypeManager->getByPrimaryKeyCached($sourceTypeId);

        $sourceTypeFilter = $this->sourceTypeFilter;

        $presenter->template->modalName = 'sourceTypeDeleteSourceTypeFromEdit';
        $presenter->template->sourceTypeModalItem = $sourceTypeFilter($sourceTypeModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeDeleteSourceTypeFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'sourceTypeDeleteSourceTypeFromEditFormYesOnClick'], true);

        $form->addHidden('sourceTypeId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceTypeDeleteSourceTypeFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('SourceType:edit', $presenter->getParameter('id'));
        }

        try {
            $this->sourceTypeManager->deleteByPrimaryKey($values->sourceTypeId);

            $presenter->flashMessage('source_type_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect(':default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
