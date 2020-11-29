<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:38
 */

namespace Rendix2\FamilyTree\App\Forms;


use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class NameForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class NameForm
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

        $form->addSelect('personId', $this->translator->translate('name_person'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('name_select_person'))
            ->setRequired('name_person_required');

        $form->addText('name', 'name_name')
            ->setRequired('name_name_required');

        $form->addText('surname', 'name_surname')
            ->setRequired('name_surname_required');

        $form->addText('nameFonetic', 'name_name_fonetic')
            ->setNullable();

        $form->addSelect('genusId', $this->translator->translate('name_genus'))
            ->setPrompt($this->translator->translate('name_select_genus'))
            ->setTranslator(null)
            ->setRequired('name_genus_required');

        $form->addCheckbox('untilNow', 'name_until_now')
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

        $form->addSubmit('send', 'name_save_name');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
