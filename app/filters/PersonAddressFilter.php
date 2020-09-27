<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressFilter.php
 * User: Tomáš Babický
 * Date: 26.09.2020
 * Time: 16:45
 */

namespace Rendix2\FamilyTree\App\Filters;


use Dibi\Row;
use Nette\Localization\ITranslator;

class PersonAddressFilter
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * AddressFilter constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Row $address
     * @return string
     */
    public function __invoke(Row $address)
    {
        $date = '';

        if ($address->dateSince && $address->dateTo) {
            $date = '(' . $address->dateSince->format('d.m.Y') . '-' . $address->dateTo->format('d.m.Y') . ')';
        } elseif ($address->dateSince && !$address->dateTo) {
            if ($address->untilNow) {
                $date = '(' . $address->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_until_now') . ')';
            } else {
                $date = '(' . $address->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_na') . ')';
            }
        } elseif (!$address->dateSince && $address->dateTo) {
            $date = '(' . $this->translator->translate('person_na') . ' - ' . $address->dateTo->format('d.m.Y') . ')';
        } else {
            if ($address->untilNow) {
                $date = '(' . $this->translator->translate('person_na') . ' - ' . $this->translator->translate('person_until_now') . ')';
            } else {
                $date = '';
            }
        }

        return $address->street . ' ' . $address->streetNumber .'/'. $address->houseNumber . ' '  . $address->zip . ' ' . $address->town . ' ' . $date;
    }
}