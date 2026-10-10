<?php
function e($value): string{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}