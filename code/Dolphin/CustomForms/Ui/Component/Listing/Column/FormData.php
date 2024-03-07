<?php

namespace Dolphin\CustomForms\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Symfony\Component\Yaml\Yaml;

class FormData extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                // Assuming your JSON data is stored in a column named 'form_data'
                $jsonData = $item['form_data'];

                // Convert JSON to YAML
                $yamlData = Yaml::parse($jsonData);

                // Generate HTML table from YAML data
                $htmlTable = '<table class="form_submission_data" style="border: 2px solid #d6d6d6">';
                foreach ($yamlData as $key => $value) {
                    // Ensure $value is not an array
                    if ($key == 'form_key' || $key == 'form_name' || $key == 'form_id') {
                        continue;
                    }

                    if (is_array($value)) {
                        $value = implode(", ", $value);
                    }
                    $htmlTable .= "<tr><td style='border: none'>$key</td><td style='border: none;'>$value</td></tr>";
                }
                $htmlTable .= '</table>';

                // Replace the JSON data with HTML table
                $item['form_data'] = $htmlTable;
            }
        }
        return $dataSource;
    }
}
