<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSourceTypeModal.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 10:37
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

use Rendix2\FamilyTree\App\Controls\Forms\SourceTypeForm;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddSourceTypeModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddSourceTypeModal extends Control
{
    /**
     * @var SourceTypeForm $sourceTypeForm
     */
    private $sourceTypeForm;

    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * PersonAddSourceTypeModal constructor.
     *
     * @param SourceTypeForm    $sourceTypeForm
     * @param SourceTypeManager $sourceTypeManager
     */
    public function __construct(
        SourceTypeForm $sourceTypeForm,
        SourceTypeManager $sourceTypeManager
    ) {
        parent::__construct();

        $this->sourceTypeForm = $sourceTypeForm;
        $this->sourceTypeManager = $sourceTypeManager;
    }

    public function render()
    {
        $this['personAddSourceTypeForm']->render();
    }

    /**
     * @return void
     */
    public function handlePersonAddSourceType()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'personAddSourceType';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddSourceTypeForm()
    {
        $form = $this->sourceTypeForm->create();

        $form->onAnchor[] = [$this, 'personAddSourceTypeFormAnchor'];
        $form->onValidate[] = [$this, 'personAddSourceTypeFormValidate'];
        $form->onSuccess[] = [$this, 'personAddSourceTypeFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddSourceTypeFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddSourceTypeFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddSourceTypeFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->sourceTypeManager->add($values);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('source_type_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
    }
}