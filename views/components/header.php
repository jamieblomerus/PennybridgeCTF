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
    <script src="/static/js/async-loading.js"></script>
    <?php Actions::do_action('head'); ?>
</head>
<body>
    <?php Actions::do_action('body_start'); ?>
    <nav>
        <h1><a class="async-loading" href="/">Pennybridge CTF</a></h1>
        <ul>
            <li><a class="async-loading" href="/utmaningar">Utmaningar</a></li>
            <li><a class="async-loading" href="/poangtavla">Poängtavla</a></li>
            <?php
            if (\PBCTF\LoginAPI::is_admin()) {
                ?>
                <li><a class="async-loading" href="/admin">Admin</a></li>
                <?php
            }
            ?>
        </ul>
        <?php
        if (\PBCTF\LoginAPI::is_logged_in()) {
            ?>
            <a class="login" href="/utloggning">Logga ut</a>
            <?php
        } else {
            ?>
            <a class="login" href="/inloggning">Logga in</a>
            <?php
        }
        ?>
    </nav>