<?php
    function renderErrorMessages($messages) {
        foreach ($messages as $message) {
            echo '<div class="feedback-red"><div class="feedback-text-line">Error: '.$message.'</div></div>';
        }
    }
?>