<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddJobModal.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 11:10
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


use Rendix2\FamilyTree\App\Controls\Forms\JobForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\JobSettings;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddJobModal extends Control
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var JobForm $jobForm
     */
    private $jobForm;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager
     */
    private $townSettingsManager;

    /**
     * PersonAddJobModal constructor.
     *
     * @param AddressFacade       $addressFacade
     * @param JobManager          $jobManager
     * @param JobForm             $jobForm
     * @param TownManager         $townManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobManager $jobManager,
        JobForm $jobForm,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->jobForm = $jobForm;

        $this->addressFacade = $addressFacade;
        $this->jobManager = $jobManager;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
    }

    public function render()
    {
        $this['personAddJobForm']->render();
    }

    /**
     * @return void
     */
    public function handlePersonAddJob()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $towns = $this->townSettingsManager->getAllPairs();
        $addresses = $this->addressFacade->getPairsCached();

        $this['personAddJobForm-townId']->setItems($towns);
        $this['personAddJobForm-addressId']->setItems($addresses);

        $presenter->template->modalName = 'personAddJob';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddJobForm()
    {
        $jobSettings = new JobSettings();

        $form = $this->jobForm->create($jobSettings);

        $form->onAnchor[] = [$this, 'personAddJobFormAnchor'];
        $form->onValidate[] = [$this, 'personAddJobFormValidate'];
        $form->onSuccess[] = [$this, 'personAddJobFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddJobFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddJobFormValidate(Form $form)
    {
        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->getPairsCached();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddJobFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->jobManager->add($values);

        $presenter->flashMessage('job_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->showModal = false;

        $presenter->redrawControl('flashes');
    }
}
