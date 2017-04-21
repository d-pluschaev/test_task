
**Test task**


**Demo**
http://46.101.202.83/demo/web/admin/#

**Used technologies**
PHP 5.6+
Symphony 3
MySQL
Bootstrap 3
JQuery
Lots of native PHP & JS

**Installation**
git clone https://github.com/d-pluschaev/test_task.git
composer install _# provide correct info_
bin/console doctrine:database:create
bin/console doctrine:schema:create

**Console import**
bin/console admin:users-import-csv data/users.csv

**Questions**
1. How to improve search performance in this application? 

`The bottleneck of this search query is duplicate email filtration. So
the first step should be: avoid duplicates in DB and eliminate 
filtration inside DB query.
Another step is to use full text search engine and handle wildcards and 
other features`
