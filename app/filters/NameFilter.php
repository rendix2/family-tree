<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameFIlter.php
 * User: Tomáš Babický
 * Date: 22.09.2020
 * Time: 20:19
 */

namespace Rendix2\FamilyTree\App\Filters;

use Dibi\Row;
use Nette\Localization\ITranslator;

/**
 * Class NameFilter
 * @package Rendix2\FamilyTree\App\Filters
 */
class NameFilter
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * NameFilter constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Row $name
     * @return string
     */
    public function __invoke(Row $name)
    {
        $date = '';

        if ($name->dateSince && $name->dateTo) {
            $date = '(' . $name->dateSince->format('y.m.dd ') . '-' . $name->dateTo->format('yy.mm.dd ') . ')';
        } elseif ($name->dateSince && !$name->dateTo) {
            if ($name->untilNow) {
                $date = '(' . $name->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_until_now') . ')';
            } else {
                $date = '(' . $name->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_na') . ')';
            }
        } elseif (!$name->dateSince && $name->dateTo) {
            $date = '(' . $this->translator->translate('person_na') . ' - ' . $name->dateTo->format('d.m.Y') . ')';
        } else {
            $date = '';
        }

        return $name->name . ' ' . $name->surname . ' ' . $date;
    }
}