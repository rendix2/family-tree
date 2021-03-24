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
 * Trait GenusPersonNameDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
class GenusDeletePersonNameModal extends Control
{
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
     * @param NameFacade $nameFacade
     * @param NameFilter $nameFilter
     * @param NameManager $nameManager
     * @param ITranslator $translator
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonFilter $personFilter
        , NameFacade $nameFacade,
        NameFilter $nameFilter,
        NameManager $nameManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->nameFacade = $nameFacade;
        $this->nameFilter = $nameFilter;
        $this->nameManager = $nameManager;
        $this->translator = $translator;
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

        if ($presenter->isAjax()) {
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
    }

    /**
     * @return Form
     */
    protected function createComponentGenusDeletePersonNameForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'genusDeletePersonNameFormYesOnClick']);
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
            $presenter->redirect('Genus:edit', $values->nameId);
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
