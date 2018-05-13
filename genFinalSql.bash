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

#for file in ${fileList[@]}; do
#	echoerr "Traitement de: ${file}"
#	cat  >> "${outfile}" << EOF
#------------------------------------------------------------
#-- fichier: ${file}
#------------------------------------------------------------

#EOF
#	cat "${file}" >> "${outfile}"
#done

for (( i=0; i < ${#fileList[@]}; i++ )); do
	if [ ! $i -eq 0 ]; then
		echo "" >> "${outFile}"
		echo "" >> "${outFile}"
	fi

	echoerr "${i}. Traitement de: ${fileList[$i]}"
	cat  >> "${outFile}" << EOF
------------------------------------------------------------
-- fichier: ${fileList[$i]}
------------------------------------------------------------

EOF
	cat "${fileList[$i]}" >> "${outFile}"
done
