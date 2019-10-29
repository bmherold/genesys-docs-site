# genesys-docs-site
Testing ground for YodaJS JS and Wordpress plugin using Trellis (Wordpress server via vagrant) & Bedrock (Wordpress boilerplate) for use inside PureCloud web-directory.

1. To use navigate to the trellis folder and $vagrant up
1. Add to hosts: 
    1. 192.168.50.5	docs.test
    1. 192.168.50.5	www.docs.test
1. Visit https://docs.test to view the local wordpress site

## Notes
You must place a copy of the yoda-guides WP plugin inside /site/web/app/plugins. Symlinking doesn't seem to work correctly inside the vagrant environment. It can be found here: https://bitbucket.org/inindca/yoda-guides/src/master/ inside the 'wp' folder.
