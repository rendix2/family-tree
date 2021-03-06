<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteWeddingParentModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:17
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Model\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonDeleteWeddingParentModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteWeddingParentModal extends Control
{
    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingFilter $weddingFilter
     */
    private $weddingFilter;

    /**
     * PersonDeleteWeddingParentModal constructor.
     *
     * @param PersonFacade        $personFacade
     * @param DeleteModalForm     $deleteModalForm
     * @param PersonUpdateService $personUpdateService
     * @param WeddingManager      $weddingManager
     * @param WeddingFacade       $weddingFacade
     * @param WeddingFilter       $weddingFilter
     */
    public function __construct(
        PersonFacade $personFacade,

        DeleteModalForm $deleteModalForm,

        PersonUpdateService $personUpdateService,
        WeddingManager $weddingManager,
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->personFacade = $personFacade;
        $this->personUpdateService = $personUpdateService;
        $this->weddingManager = $weddingManager;
        $this->weddingFacade = $weddingFacade;
        $this->weddingFilter = $weddingFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteParentsWeddingForm']->render();
    }

    /**
     * @param int $personId
     * @param int $weddingId
     */
    public function handlePersonDeleteParentsWedding($personId, $weddingId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteParentsWeddingForm']->setDefaults(
            [
                'weddingId' => $weddingId,
                'personId' => $personId
            ]
        );

        $weddingFilter = $this->weddingFilter;

        $weddingModalItem = $this->weddingFacade->select()->getCachedManager()->getByPrimaryKey($weddingId);

        $presenter->template->modalName = 'personDeleteParentsWedding';
        $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteParentsWeddingForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteParentsWeddingFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('weddingId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteParentsWeddingFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->delete()->deleteByPrimaryKey($values->weddingId);

        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($values->personId);

        $this->personUpdateService->prepareParentsWeddings($presenter, $person->father, $person->mother);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('father_wives');
        $presenter->redrawControl('mother_husbands');
    }
}
