<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSonModal.php
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

/**
 * Class PersonDeleteSonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteSonModal extends Control
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
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeleteSonModal constructor.
     *
     * @param DeleteModalForm $deleteModalForm
     * @param PersonFacade    $personFacade
     * @param PersonManager   $personManager
     * @param PersonFilter    $personFilter
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,
        PersonFacade $personFacade,
        PersonManager $personManager,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->personManager = $personManager;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteSonForm']->render();
    }

    /**
     * @param int $personId
     * @param int $sonId
     */
    public function handlePersonDeleteSon($personId, $sonId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteSonForm']->setDefaults(
            [
                'personId' => $personId,
                'sonId' => $sonId
            ]
        );

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);
        $sonModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($sonId);

        $presenter->template->modalName = 'personDeleteSon';
        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->sonModalItem = $personFilter($sonModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteSonForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteSonFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('sonId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteSonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $parent = $this->personManager->select()->getSettingsManager()->getByPrimaryKey($values->personId);

        if ($parent->gender === 'm') {
            $this->personManager->update()->updateByPrimaryKey($values->sonId, ['fatherId' => null,]);
        } elseif ($parent->gender === 'f') {
            $this->personManager->update()->updateByPrimaryKey($values->sonId, ['motherId' => null,]);
        }

        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($values->personId);

        $sons = $this->personManager->select()->getSettingsCachedManager()->getSonsByPerson($person);

        $presenter->template->sons = $sons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_son_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sons');
    }
}
