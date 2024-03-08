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
                // Assuming your YAML data is stored in a column named 'form_data'
                if (isset($item['form_data'])) {
                    $yamlData = $item['form_data'];

                    // Convert YAML to an associative array
                    $dataArray = Yaml::parse($yamlData);

                    // Format the array for display
                    $formattedYaml = Yaml::dump($dataArray);
                    // Replace the original YAML data with the formatted YAML
                    $item['form_data'] = "<pre>".$formattedYaml;
                } else {
                    $item['form_data'] = ''; // Set a default value or handle the case appropriately.
                }
            }
        }
        return $dataSource;
    }
}
