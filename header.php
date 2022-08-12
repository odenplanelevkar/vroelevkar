<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory') ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory') ?>/css/front.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="<?php echo get_bloginfo('template_directory') ?>/img/logga.png" type="image/icon type">

    <meta name="viewport" content="width=device-width; initial-scale=1.0;">
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/animate-page.js" charset="utf-8"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  </head>
  <body class="front">

    <!-- ***********************************
    * ERROR HANDLING
    *************************************-->
    <?php show_error_alert(); ?>

    <?php require_once(get_template_directory() . "/scripts/helpful_functions.php"); ?>

    <section id="front-navbar">

      <nav>
        <a href="/">Hem</a>
        <a href="/om-karen">Kåren</a>
        <a href="/front-kalender">Kalender!</a>
        <a href="/panel/dashboard">Panel</a>
      </nav>

    </section>
