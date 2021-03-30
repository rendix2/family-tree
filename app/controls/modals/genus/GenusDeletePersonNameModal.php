<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPersonNameDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:25
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
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
 * Class GenusDeletePersonNameModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus
 */
class GenusDeletePersonNameModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

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
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * GenusDeletePersonNameModal constructor.
     *
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param DeleteModalForm $deleteModalForm
     * @param NameFacade $nameFacade
     * @param NameFilter $nameFilter
     * @param NameManager $nameManager
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        DeleteModalForm $deleteModalForm,
        NameFacade $nameFacade,
        NameFilter $nameFilter,
        NameManager $nameManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->nameFacade = $nameFacade;
        $this->personFacade = $personFacade;

        $this->nameFilter = $nameFilter;
        $this->personFilter = $personFilter;

        $this->nameManager = $nameManager;
    }

    public function render()
    {
        $this['genusDeletePersonNameForm']->render();
    }

    /**
     * @param int $nameId
     * @param int $personId
     */
    public function handleGenusDeletePersonName($nameId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Genus:edit', $presenter->getParameter('id'));
        }

        $this['genusDeletePersonNameForm']->setDefaults(
            [
                'nameId' => $nameId,
                'personId' => $personId,
            ]
        );

        $personFilter = $this->personFilter;
        $nameFilter = $this->nameFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($nameId);

        $presenter->template->modalName = 'genusDeletePersonName';
        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->nameModalItem = $nameFilter($nameModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentGenusDeletePersonNameForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'genusDeletePersonNameFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('nameId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function genusDeletePersonNameFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Genus:edit', $presenter->getParameter('id'));
        }

        try {
            $this->nameManager->deleteByPrimaryKey($values->nameId);

            $genusNamePersons = $this->nameFacade->getByGenusIdCached($values->personId);

            $presenter->template->genusNamePersons = $genusNamePersons;

            $presenter->payload->showModal = false;

            $presenter->flashMessage('name_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('genus_name_persons');
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
