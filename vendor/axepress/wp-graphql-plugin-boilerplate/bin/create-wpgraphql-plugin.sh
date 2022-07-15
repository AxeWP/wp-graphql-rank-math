#!/usr/bin/env bash

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PARENT_DIRECTORY="${DIR%/*}"

DEFAULT_NAME="WPGraphQL Plugin Name"
DEFAULT_SLUG="wp-graphql-plugin-name"
DEFAULT_NAMESPACE="PluginName"
DEFAULT_PREFIX="pb"
DEFAULT_PREFIX_UPPERCASE="PB"

helpFunction() {
	echo ""
	printf "Usage: %s [--name pluginName] [--namespace pluginNamespace] [--path pluginPath] [--prefix pluginPrefix] [--slug pluginSlug] [-v|--verbose] [-?|--help]\n" "$0"
	printf "%s\t\tThe name of the plugin (e.g. WPGraphQL for MyPlugin).\n" "--name"
	printf "%s\tThe PHP namespace to be used for the plugin (e.g. MyPlugin).\n" "--namespace"
	printf "%s\t\tThe path to the wp-content/plugins directory where the plugin should be created.\n" "--path"
	printf "%s\tThe plugin prefix, in lowercase. This will be used to generate unique functions, hooks and constants (e.g. mp).\n" "--prefix"
	printf "%s\t\tThe slug (in kebab-case) to use for the plugin (e.g. wp-graphql-my-plugin).\n" "--slug"
	printf "%s\tEnable Verbose mode.\n" "-v | --verbose"
	printf "%s\tPrint this Help.\n" "-v | --help"
	exit 1 # Exit script after printing help
}

get_inputs() {
	if [ -z "$name" ]; then
		read -p "Enter your plugin name [WPGraphQL for MyPlugin]:" name
		name=${name:-"WPGraphQL for MyPlugin"}
	fi
	if [ -z "$slug" ]; then
		read -p "Enter your plugin slug [wp-graphql-my-plugin]:" slug
		slug=${slug:-"wp-graphql-my-plugin"}
	fi
	if [ -z "$namespace" ]; then
		read -p "Enter your plugin namespace [MyPlugin]:" namespace
		namespace=${namespace:-"MyPlugin"}
	fi
	if [ -z "$prefix" ]; then
		read -p "Enter your plugin prefix [mp]:" prefix
		prefix=${prefix:-"mp"}
	fi
	if [ -z "$path" ]; then
		read -p "Enter the path to your your plugin's parent folder [current folder]:" path
		path=${path:-$PWD}
	fi
}

scaffold_plugin() {
	if [ -n "$verbose" ]; then
		echo "Moving plugin boilerplate to $PLUGIN_PATH..."
	fi

	if [ -n "${TMPD}" ]; then
		mv "${TMPD}/${DEFAULT_SLUG}/${DEFAULT_SLUG}" "${PLUGIN_PATH}"
	else
		cp -R "$PARENT_DIRECTORY/${DEFAULT_SLUG}/." "$PLUGIN_PATH"
	fi

	if [ $? -ne 0 ]; then
		echo "Failed to create plugin to ${PLUGIN_PATH}, aborting."
		exit 1
	elif [ -n "$verbose" ]; then { echo "Plugin created at $PLUGIN_PATH"; }; fi

	rm -rf "$TMPD"
	echo "Installing dependencies..."
	cd "$PLUGIN_PATH" && composer install --no-interaction --quiet

	if [ $? -ne 0 ]; then
		echo "Failed to install dependencies, aborting."
		exit 1
	elif [ -n "$verbose" ]; then { echo "Dependencies installed successfully."; }; fi
}

rename_examples() {
	if [ -n "$verbose" ]; then
		echo "Renaming example files..."
	fi

	cd "${PLUGIN_PATH}" || exit
	mv .distignore-example .distignore
	mv .gitattributes-example .gitattributes
}

search_replace() {
	echo "Replacing default plugin strings..."

	if [ -n "$verbose" ]; then
		echo "The following strings will be replaced:"
		echo "${DEFAULT_SLUG} => ${slug}"
		echo "graphql_${DEFAULT_PREFIX} => graphql_${prefix}"
		echo "GRAPHQL_${DEFAULT_PREFIX_UPPERCASE} => GRAPHQL_${PREFIX_UPPERCASE}"
		echo "${DEFAULT_NAME} => ${name}"
	fi

	mv "${PLUGIN_PATH}/${DEFAULT_SLUG}.php" "${PLUGIN_PATH}/${slug}.php"

	find "$PLUGIN_PATH" -type f -not -path '*/vendor/*' | xargs sed -i "s/${DEFAULT_SLUG}/${slug}/g;
	s/${DEFAULT_NAMESPACE}/${namespace}/g;
	s/graphql_${DEFAULT_PREFIX}/graphql_${prefix}/g;
	s/graphql-${DEFAULT_PREFIX}/graphql-${prefix}/g;
	s/GRAPHQL_${DEFAULT_PREFIX_UPPERCASE}/GRAPHQL_${PREFIX_UPPERCASE}/g;
	s/${DEFAULT_NAME}/${name}/g;"
}

update_deps() {
	if [ -n "$verbose" ]; then
		echo "Updating dependencies...."
	fi

	cd "$PLUGIN_PATH" || exit
	composer update --no-interaction --quiet
}

while [ $# -gt 0 ]; do
	case $1 in
	--name) name="$2" ;;
	--namespace) namespace="$2" ;;
	--path) path="$2" ;;
	--prefix) prefix="$2" ;;
	--slug) slug="$2" ;;
	--source) TMPD="$2" ;;
	-v | --verbose) verbose="true" ;;
	-? | --help)
		helpFunction
		exit
		;;
	esac
	shift
done

get_inputs
PLUGIN_PATH="${path}"/"${slug}"
PREFIX_UPPERCASE=${prefix^^}

scaffold_plugin

if ! rename_examples; then
	echo "Failed to rename example files. Navigate to your new plugin folder and rename them manually."
elif [ -n "$verbose" ]; then { echo "Example files renamed successfully."; }; fi

if ! search_replace; then
	echo "Failed to replace plugin strings, aborting."
	exit 1
elif [ -n "$verbose" ]; then { echo "Dependencies installed successfully."; }; fi

if ! update_deps; then {
	echo "Failed to update dependencies for ${PLUGIN_SLUG}. Navigate to your new plugin folder and try running $(composer update) manually."
	exit 1
}; fi

echo "Plugin created at ${PLUGIN_PATH}"
exit 1
