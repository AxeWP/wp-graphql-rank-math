#!/bin/bash

# Activate wp-graphql
wp plugin install wp-graphql --allow-root --activate

# Activate wp-graphql-woocommerce
if [ "${INCLUDE_EXTENSIONS}" = "true" ]; then
	wp plugin install woocommerce --allow-root --activate
	wp plugin install https://github.com/wp-graphql/wp-graphql-woocommerce/archive/refs/heads/master.zip --allow-root
	# Install composer deps
	cd $WP_ROOT_FOLDER/wp-content/plugins/wp-graphql-woocommerce
	composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader

	wp plugin activate wp-graphql-woocommerce --allow-root
fi

wp plugin install seo-by-rank-math --allow-root --activate

# Activate wp-graphql-rank-math
wp plugin activate wp-graphql-rank-math --allow-root

# Set pretty permalinks.
wp rewrite structure '/%year%/%monthnum%/%postname%/' --allow-root

wp option update rank_math_registration_skip 1 --allow-root

wp db export "${DATA_DUMP_DIR}/dump.sql" --allow-root

# If maintenance mode is active, de-activate it
if $(wp maintenance-mode is-active --allow-root); then
	echo "Deactivating maintenance mode"
	wp maintenance-mode deactivate --allow-root
fi
