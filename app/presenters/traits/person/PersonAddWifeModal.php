<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddWifeModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:19
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Forms\WeddingForm;

/**
 * Trait PersonAddWifeModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddWifeModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddWife($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();

        $this['personAddWifeForm-_husbandId']->setDefaultValue($personId);
        $this['personAddWifeForm-husbandId']->setItems($males)->setDisabled()->setDefaultValue($personId);
        $this['personAddWifeForm-wifeId']->setItems($females);
        $this['personAddWifeForm-townId']->setItems($towns);

        $this->template->modalName = 'personAddWife';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
        $this->redrawControl('js');
    }

    /**
     * @param int $townId
     * @param string $formData
     */
    public function handlePersonAddWifeFormSelectTown($townId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['addressId']);

        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();

        $this['personAddWifeForm-_husbandId']->setDefaultValue($formDataParsed['_husbandId']);
        $this['personAddWifeForm-husbandId']->setItems($males)
            ->setDisabled()
            ->setDefaultValue($formDataParsed['_husbandId']);

        $this['personAddWifeForm-wifeId']->setItems($females);

        if ($townId) {
            $addresses = $this->addressFacade->getByTownPairs($townId);

            $this['personAddWifeForm-addressId']->setItems($addresses);
            $this['personAddWifeForm-townId']->setItems($towns)
                ->setDefaultValue($townId);
        } else {
            $this['personAddWifeForm-addressId']->setItems([]);
            $this['personAddWifeForm-townId']->setItems($towns)
                ->setDefaultValue(null);
        }

        $this['personAddWifeForm']->setDefaults($formDataParsed);

        $this->redrawControl('personAddWifeFormWrapper');
        $this->redrawControl('js');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddWifeForm()
    {
        $weddingSettings = new WeddingSettings();
        $weddingSettings->selectTownHandle = $this->link('personAddWifeFormSelectTown!');

        $formFactory = new WeddingForm($this->getTranslator(), $weddingSettings);

        $form = $formFactory->create();
        $form->addHidden('_husbandId');
        $form->onValidate[] = [$this, 'personAddWifeFormValidate'];
        $form->onSuccess[] = [$this, 'personAddWifeFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function personAddWifeFormValidate(Form $form)
    {
        $persons = $this->personManager->getMalesPairs($this->getTranslator());

        $husbandHiddenControl = $form->getComponent('_husbandId');

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->setValue($husbandHiddenControl->getValue())
            ->validate();

        $persons = $this->personManager->getFemalesPairs($this->getTranslator());

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons)
            ->validate();

        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->getAllPairs();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();

        $form->removeComponent($husbandHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddWifeFormSuccess(Form $form, ArrayHash $values)
    {
        $this->weddingManager->add($values);

        $person = $this->personFacade->getByPrimaryKey($this->getParameter('id'));

        $this->prepareWeddings($values->husbandId);
        $this->prepareParentsWeddings($person->father, $person->mother);

        $this->payload->showModal = false;

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('js');
        $this->redrawControl('father_wives');
        $this->redrawControl('wives');
    }
}
