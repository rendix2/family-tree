<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:37
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class SourceForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class SourceForm
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

        $form->addText('link', 'source_link')
            ->setRequired('source_link_required');

        $form->addSelect('personId', $this->translator->translate('source_person'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('source_select_person'))
            ->setRequired('source_person_required');

        $form->addSelect('sourceTypeId', $this->translator->translate('source_type'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('source_select_type'))
            ->setRequired('source_source_type_required');

        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
