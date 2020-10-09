<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DateFilter.php
 * User: Tomáš Babický
 * Date: 07.10.2020
 * Time: 22:25
 */

namespace Rendix2\FamilyTree\App\Filters;

use Dibi\Row;
use Nette\Localization\ITranslator;

/**
 * Class DateFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class DateFilter
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * DateFilter constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Row $row
     * @return string
     */
    public function __invoke(Row $row)
    {
        $date = '';

        if ($row->dateSince && $row->dateTo) {
            $date = '(' . $row->dateSince->format('d.m.Y') . '-' . $row->dateTo->format('d.m.Y') . ')';
        } elseif ($row->dateSince && !$row->dateTo) {
            if ($row->untilNow) {
                $date = '(' . $row->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('date_until_now') . ')';
            } else {
                $date = '(' . $row->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('date_na') . ')';
            }
        } elseif (!$row->dateSince && $row->dateTo) {
            $date = '(' . $this->translator->translate('date_na') . ' - ' . $row->dateTo->format('d.m.Y') . ')';
        } else {
            if ($row->untilNow) {
                $date = '(' . $this->translator->translate('date_na') . ' - ' . $this->translator->translate('date_until_now') . ')';
            } else {
                $date = $this->translator->translate('date_na');
            }
        }

        return $date;
    }
}
