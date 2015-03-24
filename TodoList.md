List of improvements and modifications to be done.

# Misc #
  * Добавить анализ статистики посещений сайта

# test #

Catchable fatal error: Object of class mysqli\_result could not be converted to string in /www/vepsian/lib/db\_text/informant/tinformant.php on line 102

# GUI #

Todo:
  * Объединить подкорпус причитаний с поиском по текстам.

Done:
  * На страницу со списком текстов - добавлены в начало страницы 5 ссылок; ссылки на подразделы на этой же страницы - отдельные подкорпуса.
  * В подкорпусе текстов убрать ссылки, оставить только checkboxes.
  * audio files subcorpus (application/x-shockwave-flash)

# Function #

TInformantVillage::getIDByName and informant\_place.php stripslashes if get\_magic\_quotes\_gpc()

Todo dictionary:
  * обратный словарь (Reverse dictionary)
  * ? how to deal with homonyms and several meanings of the word

Todo corpus:
  * Поиск по текстам (text-search) по слову "vasan" выдаёт три ответа вместо пяти.

# DB layout changes #

## DB todo ##

In future:
  1. ?

Done (top - new, bottom - old):
  1. В тексты Библии можно добавлять нумерацию деления на стихи (это просто часть текста).
  1. Добавить таблицу corpus, всего 5 корпусов (corpus\_id NOT NULL, temporary it is NULL)
  1. Добавить номер корпуса (у каждого документа, NOT NULL), всего 5 видов.
  1. Выдавать список новых (изменённых) переводов, текстов.
  1. Выдавать список новых (изменённых) лемм, словоформ. Для этого всем таблицам добавлено поле "modified".

# See also #
  * [asdf](asdf.md)