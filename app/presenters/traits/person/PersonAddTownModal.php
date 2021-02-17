<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddTown.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 22:38
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;

trait PersonAddTownModal
{
    /**
     * @return void
     */
    public function handlePersonAddTown()
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $this['personAddTownForm-countryId']->setItems($countries);

        $this->template->modalName = 'personAddTown';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddTownForm()
    {
        $formFactory = new TownForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'personAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'personAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'personAddTownFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddTownFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $this->townManager->add($values);

        $towns = $this->townSettingsManager->getAllPairsCached();

        $this['personForm-birthTownId']->setItems($towns);
        $this['personForm-deathTownId']->setItems($towns);
        $this['personForm-gravedTownId']->setItems($towns);

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('flashes');
        $this->redrawControl('personFormWrapper');
        $this->redrawControl('jsFormCallback');
    }
}
