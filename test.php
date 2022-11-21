<?php
    echo session_status();
    session_start();
    echo session_status();
    session_abort();
?>