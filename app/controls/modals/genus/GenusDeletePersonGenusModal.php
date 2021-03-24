<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPersonGenusDeleteModal.php
 * User: Tomáš Babický
 * Date: 29.11.2020
 * Time: 4:17
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait GenusPersonGenusDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
class GenusDeletePersonGenusModal extends Control
{
    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var GenusFilter $genusFilter
     */
    private $genusFilter;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * GenusDeletePersonGenusModal constructor.
     *
     * @param GenusManager $genusManager
     * @param GenusFilter $genusFilter
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param PersonManager $personManager
     * @param ITranslator $translator
     */
    public function __construct(
        GenusManager $genusManager,
        GenusFilter $genusFilter,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->genusManager = $genusManager;
        $this->genusFilter = $genusFilter;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->personManager = $personManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['genusDeletePersonGenusForm']->render();
    }

    /**
     * @param int $genusId
     * @param int $personId
     */
    public function handleGenusDeletePersonGenus($genusId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Genus:edit', $genusId);
        }

        $this['genusDeletePersonGenusForm']->setDefaults(
            [
                'genusId' => $genusId,
                'personId' => $personId,
            ]
        );

        $personFilter = $this->personFilter;
        $genusFilter = $this->genusFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $genusModalItem = $this->genusManager->getByPrimaryKeyCached($genusId);

        $presenter->template->modalName = 'genusDeletePersonGenus';
        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->genusModalItem = $genusFilter($genusModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentGenusDeletePersonGenusForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'genusDeletePersonGenusFormYesOnClick']);
        $form->addHidden('genusId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function genusDeletePersonGenusFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Genus:edit', $values->genusId);
        }

        try {
            $this->personManager->updateByPrimaryKey($values->personId, ['genusId' => null]);

            $genusPersons = $this->personFacade->getByGenusIdCached($values->personId);

            $presenter->template->genusPersons = $genusPersons;

            $presenter->payload->showModal = false;

            $presenter->flashMessage('person_genus_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('genus_persons');
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
