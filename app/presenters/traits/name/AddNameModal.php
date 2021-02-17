<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddNameModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 2:02
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Name;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\NameForm;

/**
 * Class AddNameModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Name
 */
trait AddNameModal
{
    /**
     * @return void
     */
    public function handleNameAddName()
    {
        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['nameAddNameForm-personId']->setItems($persons);
        $this['nameAddNameForm-genusId']->setItems($genuses);

        $this->template->modalName = 'nameAddName';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentNameAddNameForm()
    {
        $formFactory = new NameForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'nameAddNameFormAnchor'];
        $form->onValidate[] = [$this, 'nameAddNameFormValidate'];
        $form->onSuccess[] = [$this, 'nameAddNameFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function nameAddNameFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function nameAddNameFormValidate(Form $form)
    {
        $personControl = $form->getComponent('personId');

        $persons = $this->personManager->getAllPairs($this->translator);

        $personControl->setItems($persons)
            ->validate();

        $genusControl = $form->getComponent('genusId');

        $genuses = $this->genusManager->getPairsCached('surname');

        $genusControl->setItems($genuses)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function nameAddNameFormSuccess(Form $form, ArrayHash $values)
    {
        $this->nameManager->add($values);

        $this->flashMessage('name_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('flashes');
    }
}
