#!/usr/bin/env bash
mkdir -p uploads/actualite
sudo setfacl -Rm g:www-data:rwx var/cache var/log uploads/actualite

php ../composer.phar update

# Définir les variables nécessaires pour chaque environnement (a minima DATABASE_PASSWORD)
# A la mise en place des environnements (test ou prod), ajouter manuellement dans secrets la clef privée et le fichier 'env.DATABASE_PASSWORD.b77cc3.php'
# qui ne sont pas gités
APP_RUNTIME_ENV=dev php bin/console secrets:set DATABASE_PASSWORD
APP_RUNTIME_ENV=dev php bin/console secrets:set CAPTCHA_SECRET
APP_RUNTIME_ENV=dev php bin/console secrets:set CAPTCHA_SP_KEY
APP_RUNTIME_ENV=dev php bin/console secrets:set ENCRYPTION_KEY
APP_RUNTIME_ENV=dev php bin/console secrets:set LDAP_PASS
APP_RUNTIME_ENV=dev php bin/console secrets:set SIRENE_SECRET
APP_RUNTIME_ENV=dev php bin/console secrets:set ELISE_APPLICATION_KEY

APP_RUNTIME_ENV=test php bin/console secrets:set DATABASE_PASSWORD
APP_RUNTIME_ENV=test php bin/console secrets:set CAPTCHA_SECRET
APP_RUNTIME_ENV=test php bin/console secrets:set CAPTCHA_SP_KEY
APP_RUNTIME_ENV=test php bin/console secrets:set ENCRYPTION_KEY
APP_RUNTIME_ENV=test php bin/console secrets:set LDAP_PASS
APP_RUNTIME_ENV=test php bin/console secrets:set SIRENE_SECRET
APP_RUNTIME_ENV=test php bin/console secrets:set ELISE_APPLICATION_KEY

APP_RUNTIME_ENV=prod php bin/console secrets:set DATABASE_PASSWORD
APP_RUNTIME_ENV=prod php bin/console secrets:set CAPTCHA_SECRET
APP_RUNTIME_ENV=prod php bin/console secrets:set CAPTCHA_SP_KEY
APP_RUNTIME_ENV=prod php bin/console secrets:set ENCRYPTION_KEY
APP_RUNTIME_ENV=prod php bin/console secrets:set LDAP_PASS
APP_RUNTIME_ENV=prod php bin/console secrets:set SIRENE_SECRET
APP_RUNTIME_ENV=prod php bin/console secrets:set ELISE_APPLICATION_KEY

#php bin/console fos:js-routing:dump --format=json --target=assets/js/fos_js_routes.json

# Retirer de /importmap.php les paquets non utilisés


php bin/console importmap:install
php bin/console importmap:update
php bin/console asset-map:compile

php bin/console app:structure-update
