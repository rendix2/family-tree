<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationFom.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:37
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class RelationFom
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class RelationForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * AddressForm constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addGroup('relation_relation');

        $form->addSelect('maleId', $this->translator->translate('relation_male'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('relation_select_male'))
            ->setRequired('relation_male_required');

        $form->addSelect('femaleId', $this->translator->translate('relation_female'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('relation_select_female'))
            ->setRequired('relation_female_required');

        $form->addGroup('relation_relation_length');

        $form->addCheckbox('untilNow', 'relation_until_now')
            ->addCondition(Form::EQUAL, false)
            ->toggle('date-to');

        $form->addTbDatePicker('dateSince', 'date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'date_to')
            ->setNullable()
            ->setOption('id', 'date-to')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSubmit('send', 'relation_save_relation');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
