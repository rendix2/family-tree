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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\NameManager;
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
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

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
     * NameDeleteNameFromListModal constructor.
     *
     * @param NameFacade      $nameFacade
     * @param NameFilter      $nameFilter
     * @param DeleteModalForm $deleteModalForm
     * @param PersonFacade    $personFacade
     * @param PersonFilter    $personFilter
     * @param NameManager     $nameManager
     */
    public function __construct(
        NameFacade $nameFacade,
        NameFilter $nameFilter,

        DeleteModalForm $deleteModalForm,

        PersonFacade $personFacade,
        PersonFilter $personFilter,
        NameManager $nameManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->nameFacade = $nameFacade;
        $this->nameFilter = $nameFilter;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->nameManager = $nameManager;
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

        $nameModalItem = $this->nameFacade->select()->getCachedManager()->getByPrimaryKey($nameId);
        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);

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
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'nameDeleteNameFromListFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);
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
            $this->nameManager->delete()->deleteByPrimaryKey($values->nameId);

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
