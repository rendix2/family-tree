<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingDeleteWeddingFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:26
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class WeddingDeleteWeddingFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingDeleteWeddingFromListModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var WeddingFilter $weddingFilter
     */
    private $weddingFilter;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * WeddingDeleteWeddingFromListModal constructor.
     *
     * @param ITranslator $translator
     * @param WeddingFilter $weddingFilter
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        ITranslator $translator,
        WeddingFilter $weddingFilter,
        WeddingFacade $weddingFacade,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->weddingFilter = $weddingFilter;
        $this->weddingFacade = $weddingFacade;
        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['weddingDeleteWeddingFromListForm']->render();
    }

    /**
     * @param int $weddingId
     */
    public function handleWeddingDeleteWeddingFromList($weddingId)
    {
        if ($this->presenter->isAjax()) {
            $this['weddingDeleteWeddingFromListForm']->setDefaults(['weddingId' => $weddingId]);

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $weddingFilter = $this->weddingFilter;

            $this->presenter->template->modalName = 'weddingDeleteWeddingFromList';
            $this->presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $this->presenter->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingDeleteWeddingFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'weddingDeleteWeddingFromListFormYesOnClick']);
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function weddingDeleteWeddingFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $this->presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->presenter->redrawControl('flashes');
        }
    }
}
