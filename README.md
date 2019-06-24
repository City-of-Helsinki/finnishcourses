# Finnishcourses.fi Drupal 8 project

This project is hosted in platform.sh
https://console.platform.sh/citrusadmin/vodrxezdylpmu

## Environments

Production (master branch):
https://master-7rqtwti-vodrxezdylpmu.eu-2.platformsh.site/
https://console.platform.sh/citrusadmin/vodrxezdylpmu/master

Develop (development branch, pull requests are merged here):
https://develop-sr3snxi-vodrxezdylpmu.eu-2.platformsh.site/
https://console.platform.sh/citrusadmin/vodrxezdylpmu/develop


## Development
We use the GitHub integration of platform.sh. To contribute, clone the
repository from GitHub make a pull request:
https://github.com/digiaonline/finnishcourses-d8

Platform.sh will automatically create a running instance of the site with your
pull request in it for testing. You can find it in the platform.sh console
https://console.platform.sh/citrusadmin/vodrxezdylpmu

## Theme / CSS
The compiled CSS are not inside this repo. You need to compile them yourself
to be able to develop the site. npm version 6.9.0 should work.

```
cd web/themes/custom/finnishcourses_bootstrap
npm install
npm run-script gulp
```

## Local environment

There are several options for a local environment, you're free to choose your favourite. At the very least we recommend installing and configuring the platform.sh cli:

https://docs.platform.sh/gettingstarted/cli.html

### Bare metal with drush runserver

1. Copy .env.example to .env
2. Create a mariadb (or mysql) database, with the credentials from .env (or chose your own)
3. Some commands:

```
composer install
platform local:drush-aliases # Find your correct env
drush sql-sync @site-aliases.finnish-courses.master @baremetal
drush cim
drush runserver
drush @baremetal uli
```

4. Compile CSS as explained above

### Lando
```
brew cask install lando
lando start
lando composer install
# This will get the database from the master environment:
platform db:dump --gzip -f database.sql.gz && lando db-import database.sql.gz
# This will get the files from the master environment:
rsync -az `platform ssh --pipe -e master`:/app/web/sites/default/files/ ./web/sites/default/files
drush @lando uli
```

Compile CSS as explained above

### Acquia Dev Desktop
...
