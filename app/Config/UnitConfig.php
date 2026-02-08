<?php

namespace App\Config;

class UnitConfig
{
    /**
     * Available unit types
     */
    public const UNIT_TYPES = [
        'weight' => 'Weight',
        'money' => 'Money / Currency',
        'distance' => 'Distance',
        'volume' => 'Volume',
        'area' => 'Area',
        'energy' => 'Energy',
        'power' => 'Power',
    ];

    /**
     * All units data organized by type
     */
    protected array $unitsData = [
        'weight' => [
            'kg' => ['label_en' => 'Kilogram', 'label_fa' => 'کیلوگرم', 'symbol' => 'کیلوگرم'],
            'g' => ['label_en' => 'Gram', 'label_fa' => 'گرم', 'symbol' => 'گرم'],
            'mg' => ['label_en' => 'Milligram', 'label_fa' => 'میلی‌گرم', 'symbol' => 'میلی‌گرم'],
            'ton' => ['label_en' => 'Metric Ton', 'label_fa' => 'تن', 'symbol' => 'تن'],
            'lb' => ['label_en' => 'Pound', 'label_fa' => 'پوند', 'symbol' => 'lb'],
            'oz' => ['label_en' => 'Ounce', 'label_fa' => 'اونس', 'symbol' => 'oz'],
        ],
        'money' => [
            'IRR' => ['label_en' => 'Iranian Rial', 'label_fa' => 'ریال', 'symbol' => 'ریال'],
            'IRT' => ['label_en' => 'Iranian Toman', 'label_fa' => 'تومان', 'symbol' => 'تومان'],
            'USD' => ['label_en' => 'US Dollar', 'label_fa' => 'دلار آمریکا', 'symbol' => '$'],
            'EUR' => ['label_en' => 'Euro', 'label_fa' => 'یورو', 'symbol' => '€'],
            'GBP' => ['label_en' => 'British Pound', 'label_fa' => 'پوند انگلیس', 'symbol' => '£'],
            'JPY' => ['label_en' => 'Japanese Yen', 'label_fa' => 'ین ژاپن', 'symbol' => '¥'],
            'CNY' => ['label_en' => 'Chinese Yuan', 'label_fa' => 'یوان چین', 'symbol' => '¥'],
            'AED' => ['label_en' => 'UAE Dirham', 'label_fa' => 'درهم امارات', 'symbol' => 'د.إ'],
            'SAR' => ['label_en' => 'Saudi Riyal', 'label_fa' => 'ریال سعودی', 'symbol' => 'ر.س'],
            'TRY' => ['label_en' => 'Turkish Lira', 'label_fa' => 'لیر ترکیه', 'symbol' => '₺'],
        ],
        'distance' => [
            'km' => ['label_en' => 'Kilometer', 'label_fa' => 'کیلومتر', 'symbol' => 'کیلومتر'],
            'm' => ['label_en' => 'Meter', 'label_fa' => 'متر', 'symbol' => 'متر'],
            'cm' => ['label_en' => 'Centimeter', 'label_fa' => 'سانتی‌متر', 'symbol' => 'سانتی‌متر'],
            'mm' => ['label_en' => 'Millimeter', 'label_fa' => 'میلی‌متر', 'symbol' => 'میلی‌متر'],
            'mi' => ['label_en' => 'Mile', 'label_fa' => 'مایل', 'symbol' => 'mi'],
            'ft' => ['label_en' => 'Foot', 'label_fa' => 'فوت', 'symbol' => 'ft'],
            'in' => ['label_en' => 'Inch', 'label_fa' => 'اینچ', 'symbol' => 'in'],
        ],
        'volume' => [
            'L' => ['label_en' => 'Liter', 'label_fa' => 'لیتر', 'symbol' => 'لیتر'],
            'mL' => ['label_en' => 'Milliliter', 'label_fa' => 'میلی‌لیتر', 'symbol' => 'میلی‌لیتر'],
            'm3' => ['label_en' => 'Cubic Meter', 'label_fa' => 'متر مکعب', 'symbol' => 'متر مکعب'],
            'cm3' => ['label_en' => 'Cubic Centimeter', 'label_fa' => 'سانتی‌متر مکعب', 'symbol' => 'سانتی‌متر مکعب'],
            'gal' => ['label_en' => 'Gallon', 'label_fa' => 'گالن', 'symbol' => 'gal'],
            'qt' => ['label_en' => 'Quart', 'label_fa' => 'کوارت', 'symbol' => 'qt'],
            'pt' => ['label_en' => 'Pint', 'label_fa' => 'پاینت', 'symbol' => 'pt'],
        ],
        'area' => [
            'm2' => ['label_en' => 'Square Meter', 'label_fa' => 'متر مربع', 'symbol' => 'متر مربع'],
            'km2' => ['label_en' => 'Square Kilometer', 'label_fa' => 'کیلومتر مربع', 'symbol' => 'کیلومتر مربع'],
            'ha' => ['label_en' => 'Hectare', 'label_fa' => 'هکتار', 'symbol' => 'هکتار'],
            'acre' => ['label_en' => 'Acre', 'label_fa' => 'جریب', 'symbol' => 'acre'],
            'ft2' => ['label_en' => 'Square Foot', 'label_fa' => 'فوت مربع', 'symbol' => 'ft²'],
        ],
        'energy' => [
            'J' => ['label_en' => 'Joule', 'label_fa' => 'ژول', 'symbol' => 'ژول'],
            'kJ' => ['label_en' => 'Kilojoule', 'label_fa' => 'کیلوژول', 'symbol' => 'کیلوژول'],
            'kWh' => ['label_en' => 'Kilowatt Hour', 'label_fa' => 'کیلووات ساعت', 'symbol' => 'kWh'],
            'cal' => ['label_en' => 'Calorie', 'label_fa' => 'کالری', 'symbol' => 'cal'],
            'kcal' => ['label_en' => 'Kilocalorie', 'label_fa' => 'کیلوکالری', 'symbol' => 'kcal'],
            'BTU' => ['label_en' => 'British Thermal Unit', 'label_fa' => 'واحد حرارتی بریتانیا', 'symbol' => 'BTU'],
        ],
        'power' => [
            'W' => ['label_en' => 'Watt', 'label_fa' => 'وات', 'symbol' => 'وات'],
            'kW' => ['label_en' => 'Kilowatt', 'label_fa' => 'کیلووات', 'symbol' => 'کیلووات'],
            'MW' => ['label_en' => 'Megawatt', 'label_fa' => 'مگاوات', 'symbol' => 'مگاوات'],
            'hp' => ['label_en' => 'Horsepower', 'label_fa' => 'اسب بخار', 'symbol' => 'hp'],
        ],
    ];

    /**
     * Get all available unit types
     */
    public function getUnitTypes(): array
    {
        return self::UNIT_TYPES;
    }

    /**
     * Get units for a specific type (formatted for Select dropdown)
     */
    public function getUnitsForType(string $type, string $locale = 'fa'): array
    {
        if (! isset($this->unitsData[$type])) {
            return [];
        }

        $units = [];
        foreach ($this->unitsData[$type] as $key => $data) {
            $labelKey = 'label_'.$locale;
            $units[$key] = $data[$labelKey] ?? $data['label_en'] ?? $key;
        }

        return $units;
    }

    /**
     * Get unit data for a specific type and unit key
     */
    public function getUnitData(string $type, string $unitKey): ?array
    {
        return $this->unitsData[$type][$unitKey] ?? null;
    }

    /**
     * Get unit symbol for display
     */
    public function getUnitSymbol(string $type, string $unitKey, string $locale = 'fa'): string
    {
        $data = $this->getUnitData($type, $unitKey);
        if (! $data) {
            return $unitKey;
        }

        // Prefer symbol, fallback to localized label
        return $data['symbol'] ?? $data['label_'.$locale] ?? $data['label_en'] ?? $unitKey;
    }
}
