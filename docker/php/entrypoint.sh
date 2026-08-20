#!/bin/bash
# Note: pas de chown ici. En Podman rootless, un chown vers un autre UID
# sur un bind mount de l'host échoue silencieusement (même avec un
# subuid/subgid correctement configuré) -> chown est hors jeu dans ce
# setup. chmod, lui, fonctionne (il ne change que les bits de mode du
# fichier, pas son propriétaire). Comme www-data (uid 33 dans le
# conteneur) n'est ni propriétaire ni dans le groupe du bind mount
# (propriété de l'utilisateur host), 775 le laisse dans "other" sans
# droit d'écriture -> 777 pour que le conteneur ait toujours les droits
# d'écriture, sur n'importe quelle machine, sans étape manuelle côté host.
chmod -R 777 /var/www/html/public/uploads
chmod -R 777 /var/www/html/public/assets/stickers
exec apache2-foreground