<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddParentPartnerModal.php
 * User: Tomáš Babický
 * Date: 04.12.2020
 * Time: 0:14
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;

trait PersonAddParentPartnerFemaleModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddParentPartnerFemale($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

        $this['personAddParentPartnerFemaleForm-_maleId']->setDefaultValue($personId);
        $this['personAddParentPartnerFemaleForm-maleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddParentPartnerFemaleForm-femaleId']->setItems($persons);

        $this->template->modalName = 'personAddParentFemalePartner';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddParentPartnerFemaleForm()
    {
        $formFactory = new RelationForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_maleId');
        $form->onAnchor[] = [$this, 'personAddParentPartnerFemaleFormAnchor'];
        $form->onValidate[] = [$this, 'personAddParentPartnerFemaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddParentPartnerFemaleFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddParentPartnerFemaleFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddParentPartnerFemaleFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairsCached($this->translator);

        $maleHiddenControl = $form->getComponent('_maleId');

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons);
        $maleControl->setValue($maleHiddenControl->getValue())
            ->validate();

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($persons)
            ->validate();

        $form->removeComponent($maleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddParentPartnerFemaleFormSuccess(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $person = $this->personFacade->getByPrimaryKey($this->getParameter('id'));

        $this->prepareParentsRelations($person->father, $person->mother);

        $this->payload->showModal = false;

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('father_relations');
        $this->redrawControl('mother_relations');
    }
}
