<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:37
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class TownForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class TownForm
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

        $form->addGroup('town_town_group');

        $form->addSelect('countryId', $this->translator->translate('town_country'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('town_select_country'))
            ->setRequired('town_country_required');

        $form->addText('name', 'town_name')
            ->setRequired('town_name_required');

        $form->addText('zipCode', 'town_zip')
            ->setNullable();

        $form->addGroup('town_gps_group');

        $form->addText('gps', 'town_gps')
            ->setNullable();

        $form->addSubmit('send', 'town_save_town');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
