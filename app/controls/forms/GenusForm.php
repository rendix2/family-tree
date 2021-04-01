<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:39
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class GenusForm
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class GenusForm
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

        $form->addText('surname', 'genus_surname')
            ->setRequired('genus_surname_required');

        $form->addText('surnameFonetic', 'genus_surname_fonetic')
            ->setNullable();

        $form->addSubmit('send', 'genus_save_genus');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
