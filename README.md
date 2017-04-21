
**Test task**


**Demo**
http://46.101.202.83/demo/web/admin/

**Installation**
```bash
git clone https://github.com/d-pluschaev/test_task.git
composer install # provide correct info
bin/console doctrine:database:create
bin/console doctrine:schema:create
```

**Console import**
```bash
bin/console admin:users-import-csv data/users.csv
```

**Questions**
1. How to improve search performance in this application? 

> The bottleneck of this search query is duplicate email filtration. So
the first step should be: avoid duplicates in DB and eliminate 
filtration inside DB query.
Another step is to use full text search engine and handle wildcards and 
other features

**Used technologies**
- PHP 5.6+
- Symphony 3
- MySQL
- Bootstrap 3
- JQuery
- Lots of native PHP & JS
