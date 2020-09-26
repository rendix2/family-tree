<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingFIlter.php
 * User: Tomáš Babický
 * Date: 26.09.2020
 * Time: 13:40
 */

namespace Rendix2\FamilyTree\App\Filters;


use Dibi\Row;
use Nette\Localization\ITranslator;

class WeddingFilter
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
     * @param Row $wedding
     * @return string
     */
    public function __invoke(Row $wedding)
    {
        $date = '';

        if ($wedding->dateSince && $wedding->dateTo) {
            $date = '(' . $wedding->dateSince->format('d.m.Y') . '-' . $wedding->dateTo->format('d.m.Y') . ')';
        } elseif ($wedding->dateSince && !$wedding->dateTo) {
            if ($wedding->untilNow) {
                $date = '(' . $wedding->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_until_now') . ')';
            } else {
                $date = '(' . $wedding->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_na') . ')';
            }
        } elseif (!$wedding->dateSince && $wedding->dateTo) {
            $date = '(' . $this->translator->translate('person_na') . ' - ' . $wedding->dateTo->format('d.m.Y') . ')';
        } else {
            if ($wedding->untilNow) {
                $date = '(' . $this->translator->translate('person_na') . ' - ' . $this->translator->translate('person_until_now') . ')';
            } else {
                $date = '';
            }
        }

        return $wedding->name . ' ' . $wedding->surname . ' ' . $date;
    }
}