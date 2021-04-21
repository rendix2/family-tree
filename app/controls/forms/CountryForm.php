<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:39
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class CountryForm
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class CountryForm
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

    public function __destruct()
    {
        $this->translator = null;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addText('name', 'country_name');
        $form->addSubmit('send', 'country_save_country');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
