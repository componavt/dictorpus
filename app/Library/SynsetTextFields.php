<?php

namespace App\Library;

class SynsetTextFields
{
    /**
     * Очищает форматированный экспертный текст.
     */
    public static function clean($value)
    {
        static $purifier = null;

        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new \InvalidArgumentException(
                'Synset text must be a string or null.'
            );
        }

        if ($purifier === null) {
            $config = \HTMLPurifier_Config::createDefault();

            $config->set('Core.Encoding', 'UTF-8');

            $config->set(
                'HTML.Allowed',
                'div,p,br,strong,b,em,i,u,del,sub,sup,'
                    . 'ul,ol,li,blockquote,h1,h2,h3,pre,code,'
                    . 'a[href|title]'
            );

            $config->set('URI.AllowedSchemes', [
                'http' => true,
                'https' => true,
                'mailto' => true,
            ]);

            $config->set('Cache.DefinitionImpl', null);

            $purifier = new \HTMLPurifier($config);
        }

        $html = $purifier->purify($value);

        $plain = html_entity_decode(
            strip_tags($html),
            ENT_QUOTES,
            'UTF-8'
        );

        $plain = preg_replace(
            '/[\s\x{00A0}\x{200B}]+/u',
            '',
            $plain
        );

        return $plain === '' ? null : $html;
    }
}
