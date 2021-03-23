	DEFEDITOR=jove
	elif [ -x /usr/local/bin/jove ]; then
		DEFEDITOR=jove
	elif [ -x /usr/bin/vi ]; then
		DEFEDITOR=vi
	else
		echo "$0: No default editor found: attempting to use vi" >&2
		DEFEDITOR=vi
	fi
fi


: ${EDITOR=$DEFEDITOR}

: ${USER=${LOGNAME-`whoami`}}

trap 'rm -rf "$TEMPDIR"; exit 1' 1 2 3 13 15
