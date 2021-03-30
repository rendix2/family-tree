<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class TownDeleteTownFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteTownFromListModal extends Control
{
    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownFilter $townFilter
     */
    private $townFilter;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * TownDeleteTownFromListModal constructor.
     *
     * @param TownFacade $townFacade
     * @param TownFilter $townFilter
     * @param TownManager $townManager
     * @param ITranslator $translator
     */
    public function __construct(
        TownFacade $townFacade,
        TownFilter $townFilter,
        TownManager $townManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->townFacade = $townFacade;
        $this->townFilter = $townFilter;
        $this->townManager = $townManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['townDeleteTownFromListForm']->render();
    }

    /**
     * @param int $townId
     */
    public function handleTownDeleteTownFromList($townId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:default');
        }

        $this['townDeleteTownFromListForm']->setDefaults(['townId' => $townId]);

        $townFilter = $this->townFilter;

        $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);

        $presenter->template->modalName = 'townDeleteTownFromList';
        $presenter->template->townModalItem = $townFilter($townModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteTownFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'townDeleteTownFromListFormYesOnClick']);
        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteTownFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:default');
        }

        try {
            $this->townManager->deleteByPrimaryKey($values->townId);

            $presenter->flashMessage('town_deleted', BasePresenter::FLASH_SUCCESS);

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