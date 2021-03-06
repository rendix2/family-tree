<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddJobModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\JobForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\JobSettings;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\JobManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddJobModal extends Control
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
     * AddressAddJobModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param JobForm       $jobForm
     * @param JobManager    $jobManager
     * @param TownManager   $townManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobForm $jobForm,
        JobManager $jobManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->jobForm = $jobForm;

        $this->addressFacade = $addressFacade;
        $this->jobManager = $jobManager;
        $this->townManager = $townManager;
    }

    public function render()
    {
        $this['addressAddJobForm']->render();
    }

    /**
     * @param int $townId
     * @param int $addressId
     *
     * @return void
     */
    public function handleAddressAddJob($townId, $addressId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $addresses = $this->addressFacade->select()->getCachedManager()->getAllPairs();
        $towns = $this->townManager->select()->getSettingsCachedManager()->getAllPairs();

        $this['addressAddJobForm-_addressId']->setDefaultValue($addressId);
        $this['addressAddJobForm-addressId']->setItems($addresses)
            ->setDisabled()
            ->setDefaultValue($addressId);

        $this['addressAddJobForm-_townId']->setDefaultValue($townId);
        $this['addressAddJobForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);

        $presenter->template->modalName = 'addressAddJob';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddJobForm()
    {
        $jobSettings = new JobSettings();

        $form = $this->jobForm->create($jobSettings);

        $form->addHidden('_addressId');
        $form->addHidden('_townId');

        $form->onAnchor[] = [$this, 'addressAddJobFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddJobFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddJobFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddJobFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addressAddJobFormValidate(Form $form)
    {
        $towns = $this->townManager->select()->getCachedManager()->getAllPairs();

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->select()->getCachedManager()->getAllPairs();

        $addressHiddenControl = $form->getComponent('_addressId');

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->setValue($addressHiddenControl->getValue())
            ->validate();

        $form->removeComponent($addressHiddenControl);
        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddJobFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->jobManager->insert()->insert((array) $values);

        $jobs = $this->jobManager->select()->getSettingsManager()->getByAddressId($values->addressId);

        $presenter->template->jobs = $jobs;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('job_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jobs');
    }
}
