<?php

function my_pg_query($conn, string $query) {
    $toReturn = [];
    if (!pg_connection_busy($conn)) {
        $query_state = pg_send_query($conn, $query);
    }

    if ($query_state) {

        while ($result = pg_get_result($conn)) {
            if (pg_result_error_field($result, PGSQL_DIAG_SQLSTATE)) {
                // Une erreur est survenue (exception ?)
                throw new exception\pgQueryException($query, pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY), pg_result_error($result));
            } else {
                while ($line = my_pg_convert_result($result)) {
                    $toReturn[] = $line;
                }
                pg_free_result($result);
            }
        }
        return $toReturn;
    } else {
        // Une erreur est survenue
        throw new pgQueryException($query, pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY), pg_result_error($result));
    }
}
