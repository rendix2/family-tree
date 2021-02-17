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

trait PersonAddParentPartnerMaleModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddParentMalePartner($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

        $this['personAddParentPartnerMaleForm-_femaleId']->setDefaultValue($personId);
        $this['personAddParentPartnerMaleForm-femaleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddParentPartnerMaleForm-maleId']->setItems($persons);

        $this->template->modalName = 'personAddParentMalePartner';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddParentPartnerMaleForm()
    {
        $formFactory = new RelationForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_femaleId');
        $form->onAnchor[] = [$this, 'personAddParentPartnerMaleFormAnchor'];
        $form->onValidate[] = [$this, 'personAddParentPartnerMaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddParentPartnerMaleFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddParentPartnerMaleFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddParentPartnerMaleFormValidate(Form $form)
    {
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons)
            ->validate();

        $femaleHiddenControl = $form->getComponent('_femaleId');

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($persons)
            ->setValue($femaleHiddenControl->getValue())
            ->validate();

        $form->removeComponent($femaleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddParentPartnerMaleFormSuccess(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $this->prepareRelations($values->maleId);

        $this->payload->showModal = false;

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('father_relations');
        $this->redrawControl('mother_relations');
    }
}
