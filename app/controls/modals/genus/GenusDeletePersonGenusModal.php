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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\GenusManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class GenusDeletePersonGenusModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus
 */
class GenusDeletePersonGenusModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

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
     * GenusDeletePersonGenusModal constructor.
     *
     * @param DeleteModalForm $deleteModalForm
     * @param GenusFilter     $genusFilter
     * @param GenusManager    $genusManager
     * @param PersonFacade    $personFacade
     * @param PersonFilter    $personFilterCached
     * @param PersonManager   $personManager
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,
        GenusFilter $genusFilter,
        GenusManager $genusManager,
        PersonFacade $personFacade,
        PersonFilter $personFilterCached,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->personFacade = $personFacade;

        $this->genusFilter = $genusFilter;
        $this->personFilter = $personFilterCached;

        $this->deleteModalForm = $deleteModalForm;

        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
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
            $presenter->redirect('Genus:edit', $presenter->getParameter('id'));
        }

        $this['genusDeletePersonGenusForm']->setDefaults(
            [
                'genusId' => $genusId,
                'personId' => $personId,
            ]
        );

        $personFilter = $this->personFilter;
        $genusFilter = $this->genusFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);
        $genusModalItem = $this->genusManager->select()->getCachedManager()->getByPrimaryKey($genusId);

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
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'genusDeletePersonGenusFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

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
            $presenter->redirect('Genus:edit', $presenter->getParameter('id'));
        }

        try {
            $this->personManager->update()->updateByPrimaryKey($values->personId, ['genusId' => null]);

            $genusPersons = $this->personFacade->select()->getCachedManager()->getByGenusId($values->personId);

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
