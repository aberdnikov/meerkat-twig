Модуль Twig для MeerkatCMF
============

### template from file
~~~
$tpl = \Meerkat\Twig\Twig::from_template("news");
$tpl->set('news', ORM::factory('News')->find_all()->as_array());
echo $tpl->render();
~~~
будет произведен поиск по директориям tpl в:
* APPPATH
* MODPATH

файла под именем news.html.

Примеры путей:
* APPPATH."tpl/news.html"
* MODPATH."module_1/tpl/news.html"
* MODPATH."module_n/tpl/news.html"


### template from string
шаблон берется из входной строки, все фильтры и функции работают точно так же как и с шаблоном из файла
~~~
$tpl = \Meerkat\Twig\Twig::from_string("Меня зовут {{ name }}!");
$tpl->set('name', 'Алексей');
echo $tpl->render();
~~~

### Дополнительные функции
##### var_dump
вывод отладочного сообщения со значением переменной
~~~
{% for item in items %}
  var_dump (item)
{% endfor%}
~~~

##### config
обращение к конфигу
~~~
  config ("database.default.connection.database")
~~~
аналогично вызову
return Kohana::$config->load("database.default.connection.database")
