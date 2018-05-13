#!/bin/bash

## fct_utiles ##
echoerr() {
	echo $@ >&2
}

## DECLARATION ##
outFile="natation_cat.sql"
fileList=(
	natation_tables.sql
	natation_views.sql
	natation_functions.sql
	natation_triggers.sql
	natation_fixtures.sql
);

# Remise à zéro du fichier final
cat /dev/null > "${outFile}"

for file in ${fileList[@]}; do
	echoerr "Traitement de: ${file}"
	cat  >> "${outFile}" << EOF
------------------------------------------------------------
-- Fichier: ${file}
------------------------------------------------------------

EOF
	cat "${file}" >> "${outFile}";
done
