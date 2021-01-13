TRUE API CLI TOOLS
==========

Неофициальная консольная утилита, написанная на PHP, для работы с системой Честный Знак.

Краткий обзор
--------

Утилита позволяет просматривать и манипулировать данными системы [Честный Знак](https://xn--80ajghhoc2aj1c8b.xn--p1ai/) посредством набора официальных API:
* [TRUE API](https://честныйзнак.рф/upload/TRUE_API.pdf)
* [API ГИС МТ](https://xn--80ajghhoc2aj1c8b.xn--p1ai/upload/%D0%9E%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5+API+%D0%93%D0%98%D0%A1%D0%9C%D0%A2.pdf)
* [СУЗ API](https://suzgrid.crpt.ru/swagger-ui.html)

Зависимости
------------

- PHP 7.4+
- КриптоПРО CSP 5+
- libphpcades (является частью КриптоПРО CSP SDK)
- Действуйщий сертификат Усиленной Квалифицированной Электронной Подписи (УКЭП)

Установка
--------------------

Установка через composer:

```
composer require kilylabs/true-api-cli
```

***Установка КриптоПРО CSP 5+***

Скачать можно [отсюда](https://www.cryptopro.ru/system/files/private/csp/50/11455/linux-amd64_deb.tgz) (требуется авторизация). Вроде как в данном ПО есть бесплатный период использования, но у меня он толком не заработал. Поэтому пришлось купить официальную лицензию [здесь](https://www.cryptopro.ru/order/?online=true) (лучше брать пожизненную лицензию "Лицензия на обновление СКЗИ "КриптоПро CSP" до версии 5.0 на одном рабочем месте с доступом на портал технической поддержки" - она не на много дороже, чем годовая подписка).

Установка хорошо описана в статье: https://estp.ru/test_eds/csp_setup_linux/ (описание подходит для версии 5). 

После установки, нужно установить сертификат: https://estp.ru/test_eds/cert_install_linux/

Чтобы не быть привязанным к флешке, скопируйте папку с сертификатом УКЭП в папку:
```
cp -r /media/flash/cert.000 /var/opt/cprocsp/keys/имя_пользователя/
```

А после, установите сертификат командой:
```
/opt/cprocsp/bin/amd64/csptestf -absorb -certs
```

***Установка libphpcades***

Процедура установки описана [здесь](http://cpdn.cryptopro.ru/content/cades/phpcades-install.html)... но для PHP7 это работать на будет ) Чтобы сделать это всё работоспособным для PHP7+, нужно установить [патч](https://github.com/kilylabs/true-api-cli/tree/master/contrib/php7_support.patch). Полная процедура установки расширения примерно такая:

1) Скачиваем спец. версию cprocsp-devel пакета
```shell
wget https://www.cryptopro.ru/sites/default/files/public/faq/csp/csp5devel.tgz
tar xvzf csp5devel.tgz
cd csp5devel
dpkg -i lsb-cprocsp-devel_5.0.11863-5_all.deb
```

2) Качаем и устанавливаем КриптоПРО CADES 
```shell
wget https://www.cryptopro.ru/sites/default/files/products/cades/current_release_2_0/cades_linux_amd64.tar.gz
tar xvzf cades_linux_amd64.tar.gz
cd cades_linux_amd64
dpkg -i cprocsp-pki-phpcades-64_2.0.14071-1_amd64.deb cprocsp-pki-cades-64_2.0.14071-1_amd64.deb
```

3) Применяем специальный патч для поддержки PHP7+
```shell
cp ../true-api-cli/contrib/php7_support.patch /opt/cprocsp/src/phpcades/
cd /opt/cprocsp/src/phpcades/
patch <php7_support.patch 
```

4) В файле `/opt/cprocsp/src/phpcades/Makefile.unix` в переменную PHPDIR прописываем путь к хедерам нужной версии php
Для PHP7.4 `PHPDIR=/usr/include/php/20190902`

5) Компилируем
```shell
eval `/opt/cprocsp/src/doxygen/CSP/../setenv.sh --64`; make -f Makefile.unix
```

Если всё прошло хорошо, в каталоге будет файл libphpcades.so
В выводе php -i | grep extension_dir получаем путь к папке с расширениями и создаем там симплинк на собранную libphpcades.so
В файле php.ini пишем extension=libphpcades.so

Проверить корректность установки PHP-расширения можно с помощью команды:
```shell
# php -m|grep CPCSP
php_CPCSP
#
```

Примеры использования
------------------------

TODO
