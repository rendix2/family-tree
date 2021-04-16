<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSisterModal.php
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
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonDeleteSisterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteSisterModal extends Control
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
     * @var PersonFilter
     */
    private $personFilter;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * PersonDeleteSisterModal constructor.
     *
     * @param DeleteModalForm     $deleteModalForm
     * @param PersonFacade        $personFacade
     * @param PersonFilter        $personFilter
     * @param PersonManager       $personManager
     * @param PersonUpdateService $personUpdateServiceCached
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,

        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonUpdateService $personUpdateServiceCached
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateServiceCached;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteSisterForm']->render();
    }

    /**
     * @param int $personId
     * @param int $sisterId
     */
    public function handlePersonDeleteSister($personId, $sisterId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteSisterForm']->setDefaults(
            [
                'personId' => $personId,
                'sisterId' => $sisterId
            ]
        );

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);
        $sisterModalItem = $this->personManager->select()->getManager()->getByPrimaryKey($sisterId);

        $presenter->template->modalName = 'personDeleteSister';
        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->sisterModalItem = $personFilter($sisterModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteSisterForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteSisterFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('sisterId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteSisterFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->personManager->update()->updateByPrimaryKey($values->sisterId,
            [
                'fatherId' => null,
                'motherId' => null
            ]
        );

        $sister = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($values->sisterId);

        $this->personUpdateService->prepareBrothersAndSisters(
            $presenter,
            $values->sisterId,
            $sister->father,
            $sister->mother
        );

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_sister_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('sisters');
        $presenter->redrawControl('flashes');
    }
}
