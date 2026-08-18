#!/bin/bash
# Note: pas de chown ici. En Podman rootless, un chown vers un autre UID
# sur un bind mount de l'host échoue silencieusement (même avec un
# subuid/subgid correctement configuré). Le propriétaire réel de
# public/uploads et public/assets/stickers doit être fixé une fois côté
# host avec : podman unshare chown -R 33:33 <dossier>
chmod -R 775 /var/www/html/public/uploads
chmod -R 775 /var/www/html/public/assets/stickers
exec apache2-foreground