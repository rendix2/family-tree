<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerSecondModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:20
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;

/**
 * Trait PersonAddPartnerSecondModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddPartnerSecondModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPartnerSecond($personId)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $this['personAddPartnerSecondForm-_maleId']->setDefaultValue($personId);
        $this['personAddPartnerSecondForm-maleId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['personAddPartnerSecondForm-femaleId']->setItems($persons);

        $this->template->modalName = 'personAddPartnerSecond';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPartnerSecondForm()
    {
        $formFactory = new RelationForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_maleId');
        $form->onAnchor[] = [$this, 'personAddPartnerSecondFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPartnerSecondFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPartnerSecondFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPartnerSecondFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPartnerSecondFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

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
    public function personAddPartnerSecondFormSuccess(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $this->prepareRelations($values->maleId);

        $this->payload->showModal = false;

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->redrawControl('relation_females');
        $this->redrawControl('flashes');
    }
}
