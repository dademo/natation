<?php

/**
 * 
 * @param string $logType Type de log (erreur, exception, warning, etc)
 * @param string $errCode Code d'erreur du log (si nécéssaire)
 * @param string $logMessage Message à logger
 * @param string $at Emplacement d'où est émis le log
 * @param DateTime $when Quand a été demandé le log
 * @param array $more Des informations supplémentaires à ajouter
 */
function log_do_write(string $logType, string $errCode, string $logMessage, string $strace, DateTime $when, array $more = NULL) {
    $targetFile = __DIR__ . '/log.csv';

    if (!file_exists($targetFile)) {
        // Écriture du header
        file_put_contents(
                $targetFile, 'Type d\'erreur;'
                . 'Code d\'erreur;'
                . 'Message;'
                . 'Stacktrace;'
                . 'Date d\'émission;'
                . 'Plus'
                . "\n", FILE_APPEND | LOCK_EX
        );
    }

    // Écriture du log
    // Génération des lignes supplémentaires
    $moreLines = '';
    if (!empty($more)) {
        foreach ($more as $value) {
            $moreLines .= '"' . ((empty($moreLines)) ? '' : ';') . $value . '"';
        }
    }

    file_put_contents(
            $targetFile, $logType . '"' . ';'
            . '"' . $errCode . '"' . ';'
            . '"' . $logMessage . '"' . ';'
            . '"' . $strace . '"' . ';'
            . '"' . $when->format('d/m/Y H:i:s') . '"' . ';'
            . $moreLines
            . "\n", FILE_APPEND | LOCK_EX
    );
}

function throwable_log(Throwable $toLog) {
    log_do_write(
            get_class($toLog), $toLog->getCode(), $toLog->getMessage(), $toLog->getTraceAsString(), new DateTime('now'), NULL
    );
}
