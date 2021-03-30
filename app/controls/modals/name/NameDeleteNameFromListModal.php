<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class NameDeleteNameFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name
 */
class NameDeleteNameFromListModal extends Control
{
    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var NameFilter $nameFilter
     */
    private $nameFilter;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * NameDeleteNameFromListModal constructor.
     *
     * @param NameFacade $nameFacade
     * @param NameFilter $nameFilter
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param NameManager $nameManager
     * @param ITranslator $translator
     */
    public function __construct(
        NameFacade $nameFacade,
        NameFilter $nameFilter,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        NameManager $nameManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->nameFacade = $nameFacade;
        $this->nameFilter = $nameFilter;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->nameManager = $nameManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['nameDeleteNameFromListForm']->render();
    }

    /**
     * @param int $nameId
     * @param int $personId
     */
    public function handleNameDeleteNameFromList($nameId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Name:default');
        }

        $this['nameDeleteNameFromListForm']->setDefaults(
            [
                'personId' => $personId,
                'nameId' => $nameId
            ]
        );

        $personFilter = $this->personFilter;
        $nameFilter = $this->nameFilter;

        $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($nameId);
        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'nameDeleteNameFromList';
        $presenter->template->nameModalItem = $nameFilter($nameModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentNameDeleteNameFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'nameDeleteNameFromListFormYesOnClick']);
        $form->addHidden('nameId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function nameDeleteNameFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Name:default');
        }

        try {
            $this->nameManager->deleteByPrimaryKey($values->nameId);

            $presenter->flashMessage('name_deleted', BasePresenter::FLASH_SUCCESS);

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
