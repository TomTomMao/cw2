<?php
    function renderErrorMessages($messages, $error=true) {
        foreach ($messages as $message) {
            if ($error) {
                echo '<div class="feedback-red"><div class="feedback-text-line">Error: '.$message.'</div></div>';
            } else {
                echo '<div class="feedback-yellow"><div class="feedback-text-line">'.$message.'</div></div>';
            }
        }
    }
    function renderErrorMessage($message, $error=true) {
        if ($error) {
            echo '<div class="feedback-red"><div class="feedback-text-line">Error: '.$message.'</div></div>';
        } else {
            echo '<div class="feedback-yellow"><div class="feedback-text-line">'.$message.'</div></div>';
        }
    }
?>