<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class NameDeleteNameFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name
 */
class NameDeleteNameFromEditModal extends Control
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
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * NameDeleteNameFromEditModal constructor.
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

        DeleteModalForm $deleteModalForm,

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
        $this['nameDeleteNameFromEditForm']->render();
    }

    /**
     * @param int $nameId
     * @param int $personId
     */
    public function handleNameDeleteNameFromEdit($nameId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Name:edit', $presenter->getParameter('id'));
        }

        $this['nameDeleteNameFromEditForm']->setDefaults(
            [
                'personId' => $personId,
                'nameId' => $nameId
            ]
        );

        $personFilter = $this->personFilter;
        $nameFilter = $this->nameFilter;

        $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($nameId);
        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'nameDeleteNameFromEdit';
        $presenter->template->nameModalItem = $nameFilter($nameModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentNameDeleteNameFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'nameDeleteNameFromEditFormYesOnClick'], true);
        $form->addHidden('nameId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function nameDeleteNameFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Name:edit', $presenter->getParameter('id'));
        }

        try {
            $this->nameManager->deleteByPrimaryKey($values->nameId);

            $presenter->flashMessage('name_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('Name:default');
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
