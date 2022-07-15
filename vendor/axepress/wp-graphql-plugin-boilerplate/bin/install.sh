#!/usr/bin/env bash
set +u

helpFunction() {
	echo ""
	printf "Usage: %s [--branch githubBranch] [--name pluginName] [--namespace pluginNamespace] [--path pluginPath] [--prefix pluginPrefix] [--slug pluginSlug] [-v|--verbose] [-?|--help]\n" "$0"
	printf "%s\tThe Github branch to use as the source (e.g. master).\n" "--branch"
	printf "%s\t\tThe name of the plugin (e.g. WPGraphQL for MyPlugin).\n" "--name"
	printf "%s\tThe PHP namespace to be used for the plugin (e.g. MyPlugin).\n" "--namespace"
	printf "%s\t\tThe path to the wp-content/plugins directory where the plugin should be created.\n" "--path"
	printf "%s\tThe plugin prefix, in lowercase. This will be used to generate unique functions, hooks and constants (e.g. mp).\n" "--prefix"
	printf "%s\t\tThe slug (in kebab-case) to use for the plugin (e.g. wp-graphql-my-plugin).\n" "--slug"
	printf "%s\tEnable Verbose mode.\n" "-v | --verbose"
	printf "%s\tPrint this Help.\n" "-v | --help"
	exit 1 # Exit script after printing help
}

download() {
	if [ $(which curl) ]; then
		curl -L "$1" >"$2"
	elif [ $(which wget) ]; then
		wget -nv -O "$2" "$1"
	fi
}

download_plugin() {
	local BRANCH=${branch:-"master"}

	echo "Downloading zip to ${TMPD}..."
	download https://github.com/AxeWP/wp-graphql-plugin-boilerplate/zipball/"${BRANCH}" "$TMPD"/wp-graphql-plugin-boilerplate.zip
	if [ $? -ne 0 ]; then { echo "Failed to download, aborting." ; exit 1; } fi

	unzip -q "${TMPD}/wp-graphql-plugin-boilerplate.zip" -d "${TMPD}/wpgraphql-boilerplate"
	if [ $? -ne 0 ]; then { echo "Failed to unzip, aborting." ; exit 1; } fi
	mkdir -p ${TMPD}/wp-graphql-plugin-name
	shopt -s dotglob
	mv ${TMPD}/wpgraphql-boilerplate/*/* ${TMPD}/wp-graphql-plugin-name/
}

function do_source() {
	source "${TMPD}/wp-graphql-plugin-name/bin/create-wpgraphql-plugin.sh"
}

while [ $# -gt 0 ]; do
	case $1 in
		--branch) prefix="$2" ;;
		--name) name="$2" ;;
		--namespace) namespace="$2" ;;
		--path) path="$2" ;;
		--prefix) prefix="$2" ;;
		--slug) slug="$2" ;;
	-v | --verbose) verbose="true" ;;
	-? | --help)
		helpFunction
		exit
		;;
	esac
	shift
done

TMPD=$(mktemp -d)
# Exit if the temp directory wasn't created successfully.
if [ ! -e "$TMPD" ]; then
		>&2 echo "Failed to create temp directory, aborting."
		exit 1
fi

download_plugin
do_source "--name ${name} --namespace ${namespace} --path ${path} --prefix ${prefix} --slug ${slug} --source ${TMPD}";
