<?php

function my_pg_convert_result($result) {
    // Résultat
    $toReturn = [];
    // Récupération de la ligne
    $line = pg_fetch_array($result, null, PGSQL_NUM);

    if ($line) {
        //for($i = 0; $i < pg_num_fields($result); $i++){
        foreach ($line as $key => $value) {
            // Le type de champ
            $fieldType = pg_field_type($result, $key);
            // Le réultat est-il un tableau ?
            $isArray = ($fieldType[0] == '_');
            // Donnée temporaire, à ajouter à toReturn
            $foreach_val = ($isArray) ? [] : null;

            if ($isArray) {
                $fieldType = substr($fieldType, 1);
            }

            switch ($fieldType) {
                case 'bool':
                case 'boolean':
                    if ($isArray) {
                        //explode(',', trim($pgArray['strings'], '{}'));
                        // voir: https://stackoverflow.com/questions/3068683/convert-postgresql-array-to-php-array
                        foreach ((explode(',', trim($value, '{}'))) as $array_elem) {
                            $foreach_val[] = ($array_elem == 't');
                        }
                    } else {
                        $foreach_val = ($value == 't');
                    }
                    break;

                // Numeric types
                case 'integer':
                case 'int':
                case 'int2':
                case 'int4':
                case 'int8':
                case 'bigint':
                    if ($isArray) {
                        foreach ((explode(',', trim($value, '{}'))) as $array_elem) {
                            $foreach_val[] = intval($array_elem);
                        }
                    } else {
                        $foreach_val = intval($value);
                    }

                    break;

                case 'decimal':
                case 'numeric':
                case 'real':
                case 'double precision':
                case 'float8':
                case 'smallserial':
                case 'serial':
                case 'serial2':
                case 'serial4':
                case 'serial8':
                case 'bigserial':
                    if ($isArray) {
                        foreach ((explode(',', trim($value, '{}'))) as $array_elem) {
                            $foreach_val[] = doubleval($array_elem);
                        }
                    } else {
                        $foreach_val = doubleval($value);
                    }

                    break;
                // Date / Time
                case 'timestamp':
                case 'date':
                case 'time':
                case 'timetz':
                case 'timestamptz':
                    if ($isArray) {
                        foreach ((explode(',', trim($value, '{}'))) as $array_elem) {
                            $foreach_val[] = new DateTime($array_elem);
                        }
                    } else {
                        $foreach_val = new DateTime($value);
                    }

                    break;

                case 'interval':
                    if ($isArray) {
                        foreach ((explode(',', trim($value, '{}'))) as $array_elem) {
                            $foreach_val[] = new DateInterval($array_elem);
                        }
                    } else {
                        $foreach_val = new DateInterval($value);
                    }

                    break;

                // default: on traite comme une string
                default:
                    if ($isArray) {
                        foreach ((explode(',', trim($value, '{}'))) as $array_elem) {
                            $foreach_val[] = $array_elem;
                        }
                    } else {
                        $foreach_val = $value;
                    }

                    break;
            }

            $toReturn[pg_field_name($result, $key)] = $foreach_val;
        }
    } else {
        return $line;
    }

    return $toReturn;

    //return (pg_fetch_assoc($result));
}
