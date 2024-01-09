<?php
namespace PBCTF\Views\Components;

use PBCTF\Actions;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php Actions::do_action('title'); ?></title>
    <link rel="stylesheet" href="/static/stylesheets/main.css">
    <?php Actions::do_action('head'); ?>
</head>
<body>
    <?php Actions::do_action('body_start'); ?>
    <nav>
        <h1><a href="/">Pennybridge CTF</a></h1>
        <ul>
            <li><a href="/utmaningar">Utmaningar</a></li>
            <li><a href="/poangtavla">Poängtavla</a></li>
        </ul>
        <a class="login" href="/login">Logga in</a>
    </nav>