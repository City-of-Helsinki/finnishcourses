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
