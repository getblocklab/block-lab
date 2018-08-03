-include ./tests/docker/.env

# Required because we need these vars in a non-typical docker-compose way.
WPDIR=WP_INSTALL_DIR=${WP_INSTALL_DIR}
WPVER=WP_VERSION=${WP_VERSION}
WPTEST=WP_TESTS_DIR=${WP_TESTS_DIR}
WPPLUGIN=WP_PLUGIN_FILE=${WP_PLUGIN_FILE}
PHPUNITARGS=PHPUNIT_ARGS="${PHPUNIT_ARGS}"
WPENV=${WPDIR} ${WPVER} ${WPTEST} ${WPPLUGIN} ${PHPUNITARGS}

# Show available make commands.
usage:
	@echo "Usage:\n  make [command]\n\nCommands:"
	@echo "  test\t\tRun test suite"

test:
	@echo "Updating WordPress Test Suite..."
	@svn co https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/ ./tests/includes --trust-server-cert --non-interactive -q
	@svn co https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/data/ ./tests/data --trust-server-cert --non-interactive -q
	@${WPENV} docker-compose -f tests/docker/docker-compose.yml up -d tests-mysql
	@${WPENV} docker-compose -f tests/docker/docker-compose.yml up tests-php
	@${WPENV} docker-compose -f tests/docker/docker-compose.yml down

gulp:
	@echo "Installing Gulp 4.0..."
	@npm install -g gulp@next
	@echo "Updating local packages."
	@npm update
	@echo "Run 'gulp' to build your package."
