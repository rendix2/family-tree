<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingForm.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:23
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class WeddingForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class WeddingForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * WeddingForm constructor.
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
        $form->addSelect('husbandId', $this->translator->translate('wedding_husband'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('wedding_select_husband'))
            ->setRequired('wedding_husband_required');

        $form->addSelect('wifeId', $this->translator->translate('wedding_wife'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('wedding_select_wife'))
            ->setRequired('wedding_wife_required');

        $form->addCheckbox('untilNow', 'wedding_until_now')
            ->addCondition(Form::EQUAL, false)
            ->toggle('date-to');

        $form->addTbDatePicker('dateSince', 'date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'date_to')
            ->setOption('id', 'date-to')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSelect('townId', $this->translator->translate('wedding_town'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('wedding_select_town'));

        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
