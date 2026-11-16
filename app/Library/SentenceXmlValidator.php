<?php

namespace App\Library;

/**
 * Проверяет редактируемое предложение без исправления исходной строки.
 * Общий Text::toXML() намеренно не изменяется.
 */
class SentenceXmlValidator
{
    public static function error($xml, $s_id)
    {
        if (!is_string($xml) || trim($xml) === '') {
            return 'XML предложения не должен быть пустым.';
        }
        if (strpos($xml, "\0") !== false || !preg_match('//u', $xml)) {
            return 'XML предложения должен быть строкой UTF-8 без нулевых байтов.';
        }
        if (preg_match('/<!\s*(?:DOCTYPE|ENTITY)\b/i', $xml)) {
            return 'DOCTYPE и объявления ENTITY в предложении запрещены.';
        }

        $previous_mode = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->resolveExternals = false;
        $dom->substituteEntities = false;
        $dom->validateOnParse = false;

        try {
            $loaded = $dom->loadXML($xml, LIBXML_NONET);
            $errors = libxml_get_errors();
            if (!$loaded || count($errors) > 0) {
                $detail = count($errors) > 0
                    ? ' Строка ' . (int) $errors[0]->line . ': ' . trim($errors[0]->message)
                    : '';
                return 'Некорректный XML предложения.' . $detail;
            }
            if ($dom->doctype !== null) {
                return 'DOCTYPE и объявления ENTITY в предложении запрещены.';
            }
            if ($dom->xmlEncoding && strcasecmp($dom->xmlEncoding, 'UTF-8') !== 0) {
                return 'Для XML предложения допустима только кодировка UTF-8.';
            }
            $root = $dom->documentElement;
            if (!$root || $root->tagName !== 's' || $root->namespaceURI) {
                return 'Корневой элемент предложения должен быть <s id="...">.';
            }
            $raw_s_id = $root->getAttribute('id');
            $parsed_s_id = filter_var($raw_s_id, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);
            if ($parsed_s_id === false || $parsed_s_id !== (int) $s_id) {
                return 'Номер <s id="..."> должен совпадать с номером предложения '
                    . (int) $s_id . '.';
            }

            $xpath = new \DOMXPath($dom);
            $seen = [];
            $stack = [$dom];
            while ($stack) {
                $node = array_pop($stack);
                if ($node->nodeType === XML_PI_NODE
                    || $node->nodeType === XML_COMMENT_NODE
                    || $node->nodeType === XML_ENTITY_REF_NODE) {
                    return 'Комментарии, инструкции обработки и ссылки на объявленные сущности запрещены.';
                }
                if ($node instanceof \DOMElement) {
                    $tag = $node->tagName;
                    if ($node->namespaceURI || !in_array($tag, ['s', 'w', 'b', 'sup', 'br'], true)) {
                        return 'Недопустимый элемент <' . $tag . '> в XML предложения.';
                    }
                    if ($tag === 's' && !$node->isSameNode($root)) {
                        return 'Вложенные предложения <s> запрещены.';
                    }

                    // Объявления xmlns не перечисляются как обычные атрибуты.
                    // Встроенный префикс xml существует даже без объявления.
                    $namespaces = $xpath->query('namespace::*[name() != "xml"]', $node);
                    if ($namespaces === false || $namespaces->length > 0
                        || $node->hasAttribute('xmlns')) {
                        return 'Недопустимый атрибут xmlns у элемента <' . $tag . '>.';
                    }

                    foreach ($node->attributes as $attribute) {
                        if ($attribute->namespaceURI
                            || !in_array($tag, ['s', 'w'], true)
                            || $attribute->nodeName !== 'id') {
                            return 'Недопустимый атрибут ' . $attribute->nodeName
                                . ' у элемента <' . $tag . '>.';
                        }
                    }
                    if ($tag === 'br' && $node->hasChildNodes()) {
                        return 'Элемент <br/> должен быть пустым.';
                    }
                    if ($tag === 'w') {
                        $raw_w_id = $node->getAttribute('id');
                        $w_id = filter_var($raw_w_id, FILTER_VALIDATE_INT, [
                            'options' => ['min_range' => 1],
                        ]);
                        if ($w_id === false) {
                            return 'Invalid word id "' . $raw_w_id
                                . '" in sentence ' . $s_id . '.';
                        }
                        if (isset($seen[$w_id])) {
                            return 'Duplicate word id ' . $w_id . ' in sentence ' . $s_id
                                . '. No words or annotation links were changed.';
                        }
                        $seen[$w_id] = true;
                        if (!preg_match('/[^\s\p{Z}\x{200B}\x{FEFF}]/u', $node->textContent)) {
                            return 'Слово <w id="' . $raw_w_id . '"> не должно быть пустым.';
                        }
                        foreach ($node->childNodes as $child) {
                            if ($child instanceof \DOMElement) {
                                return 'Внутри <w id="' . $raw_w_id
                                    . '"> недопустимы вложенные элементы. '
                                    . 'Теги оформления размещайте снаружи <w>.';
                            }
                        }
                    }
                }
                // Обход в исходном порядке, чтобы сообщать первую ошибку.
                for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
                    $stack[] = $node->childNodes->item($i);
                }
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous_mode);
        }

        return null;
    }
}
