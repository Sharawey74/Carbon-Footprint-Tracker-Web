<?php
namespace Services;

use NumberFormatter;

class LanguageService {
    private $currentLocale = 'en';
    private $messages = [
        'en' => [
            'report.title' => 'Carbon Footprint Report',
            'report.branch.info' => 'Branch Information',
            'report.branch.id' => 'Branch ID',
            'report.city' => 'City',
            'report.emissions' => 'Emissions',
            'report.production.emissions' => 'Production Emissions',
            'report.packaging.emissions' => 'Packaging Emissions',
            'report.distribution.emissions' => 'Distribution Emissions',
            'report.total.emissions' => 'Total Emissions',
            'report.emissions.breakdown' => 'Emissions Breakdown',
            'report.production' => 'Production',
            'report.packaging' => 'Packaging',
            'report.distribution' => 'Distribution',
            'report.generated.on' => 'Generated on',
            'report.comparative.title' => 'Comparative Carbon Footprint Report',
            'report.comparative.summary' => 'Summary of %d branches with total emissions of %s kg CO₂ (average %s kg per branch)',
            'report.location' => 'Location',
            'report.employees' => 'Employees',
            'report.emissions.per.employee' => 'Emissions per Employee',
            'report.top.performers' => 'Top Performers',
            'report.branch' => 'Branch',
            'report.city.title' => 'City Carbon Footprint Report'
        ],
        'ar' => [
            'report.title' => 'تقرير البصمة الكربونية',
            // Arabic translations
        ]
    ];

    public function setLanguage($languageCode) {
        $this->currentLocale = in_array($languageCode, ['en', 'ar']) ? $languageCode : 'en';
    }

    public function getMessage($key) {
        return $this->messages[$this->currentLocale][$key] ?? $key;
    }

    public function getCurrentLanguage() {
        return $this->currentLocale;
    }

    public function formatNumber($number) {
        $formatter = new NumberFormatter($this->currentLocale, NumberFormatter::DECIMAL);
        return $formatter->format($number);
    }

    public function isRTL() {
        return $this->currentLocale === 'ar';
    }
}
?>