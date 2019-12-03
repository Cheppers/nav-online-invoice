<?php

namespace NavOnlineInvoice\Traits;

trait AddQueryData
{
    protected function addQueryData(\SimpleXMLElement $xmlNode, $type, $data) {
        $node = $xmlNode->addChild($type);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->addQueryData($node, $key, $value);
            } else {
                $node->addChild($key, $value);
            }
        }
    }
}
