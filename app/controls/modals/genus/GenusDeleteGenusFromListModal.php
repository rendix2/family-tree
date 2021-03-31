<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
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

use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class GenusDeleteGenusFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus
 */
class GenusDeleteGenusFromListModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var GenusFilter $genusFilter
     */
    private $genusFilter;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * GenusDeleteGenusFromListModal constructor.
     *
     * @param GenusFilter     $genusFilter
     * @param DeleteModalForm $deleteModalForm
     * @param GenusManager    $genusManager
     */
    public function __construct(
        GenusFilter $genusFilter,

        DeleteModalForm $deleteModalForm,

        GenusManager $genusManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->genusFilter = $genusFilter;
        $this->genusManager = $genusManager;
    }

    public function render()
    {
        $this['genusDeleteGenusFromListForm']->render();
    }

    /**
     * @param int $genusId
     */
    public function handleGenusDeleteGenusFromList($genusId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Genus:default');
        }

        $this['genusDeleteGenusFromListForm']->setDefaults(['genusId' => $genusId]);

        $genusFilter = $this->genusFilter;
        $genusModalItem = $this->genusManager->getByPrimaryKeyCached($genusId);

        $presenter->template->modalName = 'genusDeleteGenusFromList';
        $presenter->template->genusModalItem = $genusFilter($genusModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentGenusDeleteGenusFromListForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'genusDeleteGenusFromListFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('genusId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function genusDeleteGenusFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Genus:default');
        }

        try {
            $this->genusManager->deleteByPrimaryKey($values->genusId);

            $presenter->flashMessage('genus_deleted', BasePresenter::FLASH_SUCCESS);

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
