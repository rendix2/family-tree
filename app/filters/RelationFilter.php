<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationFIlter.php
 * User: Tomáš Babický
 * Date: 26.09.2020
 * Time: 13:48
 */

namespace Rendix2\FamilyTree\App\Filters;


use Dibi\Row;
use Nette\Localization\ITranslator;

class RelationFilter
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
     * @param Row $relation
     * @return string
     */
    public function __invoke(Row $relation)
    {
        $date = '';

        if ($relation->dateSince && $relation->dateTo) {
            $date = '(' . $relation->dateSince->format('d.m.Y') . '-' . $relation->dateTo->format('d.m.Y') . ')';
        } elseif ($relation->dateSince && !$relation->dateTo) {
            if ($relation->untilNow) {
                $date = '(' . $relation->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_until_now') . ')';
            } else {
                $date = '(' . $relation->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_na') . ')';
            }
        } elseif (!$relation->dateSince && $relation->dateTo) {
            $date = '(' . $this->translator->translate('person_na') . ' - ' . $relation->dateTo->format('d.m.Y') . ')';
        } else {
            $date = '';
        }

        return $relation->name . ' ' . $relation->surname . ' ' . $date;
    }
}