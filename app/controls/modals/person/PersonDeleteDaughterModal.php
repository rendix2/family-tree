<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteDaughterMoodal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:08
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;

/**
 * Class PersonDeleteDaughterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteDaughterModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeleteDaughterModal constructor.
     *
     * @param DeleteModalForm $deleteModalForm
     * @param PersonManager   $personManager
     * @param PersonFacade    $personFacade
     * @param PersonFilter    $personFilterCached
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,
        PersonManager $personManager,
        PersonFacade $personFacade,
        PersonFilter $personFilterCached
    ) {
        parent::__construct();

        $this->personManager = $personManager;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilterCached;

        $this->deleteModalForm = $deleteModalForm;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteDaughterForm']->render();
    }

    /**
     * @param int $personId
     * @param int $daughterId
     */
    public function handlePersonDeleteDaughter($personId, $daughterId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteDaughterForm']->setDefaults(
            [
                'personId' => $personId,
                'daughterId' => $daughterId
            ]
        );

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);
        $daughterModalItem = $this->personManager->select()->getManager()->getByPrimaryKey($daughterId);

        $presenter->template->modalName = 'personDeleteDaughter';
        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->daughterModalItem = $personFilter($daughterModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteDaughterForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteDaughterFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);
        $form->addHidden('personId');
        $form->addHidden('daughterId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteDaughterFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $parent = $this->personManager->select()->getManager()->getByPrimaryKey($values->personId);

        if ($parent->gender === 'm') {
            $this->personManager->update()->updateByPrimaryKey($values->daughterId, ['fatherId' => null,]);
        } elseif ($parent->gender === 'f') {
            $this->personManager->update()->updateByPrimaryKey($values->daughterId, ['motherId' => null,]);
        }

        $daughters = $this->personManager->select()->getSettingsCachedManager()->getDaughtersByPerson($parent);

        $presenter->template->daughters = $daughters;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_daughter_deleted');

        $presenter->redrawControl('daughters');
        $presenter->redrawControl('flashes');
    }
}
