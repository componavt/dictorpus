<?php
// Скрываем предупреждения об устаревшем коде в Tinker, сохраняя остальные сообщения.
return [
    'errorLoggingLevel' => E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED,
];