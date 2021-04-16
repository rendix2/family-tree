<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteGenusModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

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

/**
 * Class PersonDeleteGenusModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteGenusModal extends Control
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
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var GenusFilter $genusFilter
     */
    private $genusFilter;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeleteGenusModal constructor.
     *
     * @param PersonFacade         $personFacade
     * @param DeleteModalForm      $deleteModalForm
     * @param PersonManager        $personManager
     * @param GenusManager         $genusManager
     * @param GenusFilter          $genusFilter
     * @param PersonFilter         $personFilter
     */
    public function __construct(
        PersonFacade $personFacade,
        DeleteModalForm $deleteModalForm,
        PersonManager $personManager,
        GenusManager $genusManager,
        GenusFilter $genusFilter,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->genusManager = $genusManager;
        $this->genusFilter = $genusFilter;
        $this->personFilter = $personFilter;

        $this->deleteModalForm = $deleteModalForm;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteGenusForm']->render();
    }

    /**
     * @param int $personId
     * @param int $currentGenusId
     * @param int $deleteGenusPersonId
     */
    public function handlePersonDeleteGenus($personId, $currentGenusId, $deleteGenusPersonId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteGenusForm']->setDefaults(
            [
                'genusId' => $currentGenusId,
                'personId' => $personId,
                'deleteGenusPersonId' => $deleteGenusPersonId,
            ]
        );

        $personFilter = $this->personFilter;
        $genusFilter = $this->genusFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($deleteGenusPersonId);
        $genusModalItem = $this->genusManager->select()->getManager()->getByPrimaryKey($currentGenusId);

        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->genusModalItem = $genusFilter($genusModalItem);
        $presenter->template->modalName = 'personDeleteGenus';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteGenusForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteGenusFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('genusId');
        $form->addHidden('deleteGenusPersonId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteGenusFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->personManager->update()->updateByPrimaryKey($values->deleteGenusPersonId, ['genusId' => null]);

        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($values->personId);

        $genusPersons = [];

        if ($person->genus) {
            $genusPersons = $this->personFacade->select()->getSettingsCachedManager()->getByGenusId($person->genus->id);
        }

        $presenter->template->genusPersons = $genusPersons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

        if ($values->personId === $values->deleteGenusPersonId) {
            $presenter['personForm-genusId']->setDefaultValue(null);

            $presenter->redrawControl('personFormWrapper');
        }

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('genus_persons');
        $presenter->redrawControl('jsFormCallback');
    }
}
