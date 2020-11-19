<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:37
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class SourceTypeForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class SourceTypeForm
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

        $form->addText('name', 'source_type_name')
            ->setRequired('source_type_name_required');

        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
