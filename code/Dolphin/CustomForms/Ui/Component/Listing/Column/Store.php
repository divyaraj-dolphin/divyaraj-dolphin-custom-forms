<?php

namespace Dolphin\CustomForms\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Framework\UrlInterface;

class Store extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SystemStore $systemStore,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $systemStore, $escaper, $components, $data, 'store_ids');
        $this->urlBuilder = $urlBuilder;
    }

    protected function prepareItem(array $item)
    {
        $content = '';
        $origStores = $item[$this->storeKey] ?? null;

        if (!is_array($origStores)) {
            $origStores = explode(',', $origStores);
        }
        if (in_array(0, $origStores) && count($origStores) == 1) {
            return __('All Store Views');
        }

        $data = $this->systemStore->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $content .= $website['label'] . "<br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . $this->escaper->escapeHtml($group['label']) . "<br/>";
                foreach ($group['children'] as $store) {
                    $storeId = $store['id'] ?? null; // Check if 'id' key is present
                    $storeLabel = $this->escaper->escapeHtml($store['label'] ?? '');
                    $storeUrl = $storeId ? $this->getStoreUrl($storeId) : '#'; // Use '#' if 'id' is not present
                    $content .= str_repeat('&nbsp;', 6) . "<a href=\"$storeUrl\">$storeLabel</a><br/>";
                }
            }
        }

        return $content;
    }


    protected function getStoreUrl($storeId)
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_store/edit',
            ['store_id' => $storeId]
        );
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }
        return $dataSource;
    }
}
