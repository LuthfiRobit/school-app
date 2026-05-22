<?php
$a = null;
try {
    echo $a->id ?? 'empty';
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
