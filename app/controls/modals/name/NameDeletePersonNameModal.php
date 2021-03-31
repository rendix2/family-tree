<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameDeletePersonNameModal.php
 * User: Tomáš Babický
 * Date: 29.10.2020
 * Time: 15:52
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class NameDeletePersonNameModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name
 */
class NameDeletePersonNameModal extends Control
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
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * NameDeletePersonNameModal constructor.
     *
     * @param NameFacade      $nameFacade
     * @param NameFilter      $nameFilter
     * @param DeleteModalForm $deleteModalForm
     * @param NameManager     $nameManager
     * @param PersonFacade    $personFacade
     * @param PersonFilter    $personFilter
     */
    public function __construct(
        NameFacade $nameFacade,
        NameFilter $nameFilter,

        DeleteModalForm $deleteModalForm,

        NameManager $nameManager,
        PersonFacade $personFacade,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->nameFacade = $nameFacade;
        $this->nameFilter = $nameFilter;
        $this->nameManager = $nameManager;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
    }

    public function render()
    {
        $this['nameDeletePersonNameForm']->render();
    }

    /**
     * @param int $currentNameId
     * @param int $deleteNameId
     * @param int $personId
     */
    public function handleNameDeletePersonName($currentNameId, $deleteNameId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Name:edit', $presenter->getParameter('id'));
        }

        $this['nameDeletePersonNameForm']->setDefaults(
            [
                'currentNameId' => $currentNameId,
                'deleteNameId' => $deleteNameId,
                'personId' => $personId
            ]
        );

        if ($currentNameId === $deleteNameId) {
            $this['nameDeletePersonNameForm-yes']->setAttribute('data-naja-force-redirect', '');
        }

        $personFilter = $this->personFilter;
        $nameFilter = $this->nameFilter;

        $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($deleteNameId);
        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'nameDeletePersonName';
        $presenter->template->nameModalItem = $nameFilter($nameModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentNameDeletePersonNameForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'nameDeletePersonNameFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('currentNameId');
        $form->addHidden('deleteNameId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function nameDeletePersonNameFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Name:edit', $presenter->getParameter('id'));
        }

        try {
            $this->nameManager->deleteByPrimaryKey($values->deleteNameId);

            $presenter->payload->showModal = false;

            $presenter->flashMessage('name_deleted', BasePresenter::FLASH_SUCCESS);

            if ($values->currentNameId === $values->deleteNameId) {
                $presenter->redirect('Name:default');
            } else {
                $presenter->redrawControl('flashes');
                $presenter->redrawControl('names');
            }
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
