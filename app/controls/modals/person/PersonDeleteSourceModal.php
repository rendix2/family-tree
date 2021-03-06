<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSourceModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Model\Managers\SourceManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonDeleteSourceModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteSourceModal  extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

    /**
     * @var SourceManager $sourceManager
     */
    private $sourceManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var SourceFilter $sourceFilter
     */
    private $sourceFilter;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeleteSourceModal constructor.
     *
     * @param SourceFacade    $sourceFacade
     * @param DeleteModalForm $deleteModalForm
     * @param SourceManager   $sourceManager
     * @param PersonFacade    $personFacade
     * @param SourceFilter    $sourceFilter
     * @param PersonFilter    $personFilterCached
     */
    public function __construct(
        SourceFacade $sourceFacade,

        DeleteModalForm $deleteModalForm,

        SourceManager $sourceManager,
        PersonFacade $personFacade,
        SourceFilter $sourceFilter,
        PersonFilter $personFilterCached
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;
        $this->sourceFacade = $sourceFacade;
        $this->sourceManager = $sourceManager;
        $this->personFacade = $personFacade;
        $this->sourceFilter = $sourceFilter;
        $this->personFilter = $personFilterCached;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteSourceForm']->render();
    }

    /**
     * @param int $personId
     * @param int $sourceId
     */
    public function handlePersonDeleteSource($personId, $sourceId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteSourceForm']->setDefaults(
            [
                'personId' => $personId,
                'sourceId' => $sourceId
            ]
        );

        $personFilter = $this->personFilter;
        $sourceFilter = $this->sourceFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);
        $sourceModalItem = $this->sourceFacade->select()->getCachedManager()->getByPrimaryKey($sourceId);

        $presenter->template->modalName = 'personDeleteSource';
        $presenter->template->sourceModalItem = $sourceFilter($sourceModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteSourceForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteSourceFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteSourceFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->sourceManager->delete()->deleteByPrimaryKey($values->sourceId);

        $sources = $this->sourceFacade->select()->getManager()->getByPersonId($values->personId);

        $presenter->template->sources = $sources;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('source_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sources');
    }
}
