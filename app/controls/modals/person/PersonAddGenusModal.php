<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddGenusModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:57
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\GenusForm;
use Rendix2\FamilyTree\App\Model\Managers\GenusManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddGenusModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddGenusModal extends Control
{
    /**
     * @var GenusForm $genusForm
     */
    private $genusForm;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * PersonAddGenusModal constructor.
     *
     * @param GenusForm    $genusForm
     * @param GenusManager $genusManager
     */
    public function __construct(
        GenusForm $genusForm,
        GenusManager $genusManager
    ) {
        parent::__construct();

        $this->genusForm = $genusForm;
        $this->genusManager = $genusManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddGenusForm']->render();
    }

    /**
     * @return void
     */
    public function handlePersonAddGenus()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'personAddGenus';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddGenusForm()
    {
        $form = $this->genusForm->create();

        $form->onAnchor[] = [$this, 'personAddGenusFormAnchor'];
        $form->onValidate[] = [$this, 'personAddGenusFormValidate'];
        $form->onSuccess[] = [$this, 'personAddGenusFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddGenusFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddGenusFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddGenusFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->genusManager->insert()->insert((array) $values);

        $genuses = $this->genusManager->select()->getManager()->getPairs('surname');

        $presenter['personForm-genusId']->setItems($genuses);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('genus_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('personFormWrapper');
        $presenter->redrawControl('ks');
    }
}
