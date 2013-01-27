<!DOCTYPE html>
<html lang="en-US" class="no-getusermedia">
<head>
	<meta charset="utf-8" />
	<!-- <meta name="viewport" content="width=device-width" /> -->
	<meta name="viewport" content="width=600" />
	<title><?PHP echo title(); ?></title>

	<link href="http://fonts.googleapis.com/css?family=Vampiro+One|Oxygen:400,300,700" rel="stylesheet" />
	<link href="<?PHP echo theme_url('css/bootstrap.min.css'); ?>" rel="stylesheet" />
	<link href="<?PHP echo theme_url('css/style.css'); ?>" rel="stylesheet" />
	<link href="<?PHP echo site_url(); ?>" rel="home" />

	<link rel="shortcut icon" sizes="32x32" href="<?PHP echo theme_url('img/icon-32.png'); ?>" />
	<link rel="apple-touch-icon" sizes="114x114" href="<?PHP echo theme_url('img/apple-touch-icon-114x114.png'); ?>" />

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?PHP echo theme_url('js/jquery.min.js'); ?>"><\/script>')</script>

	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?PHP echo theme_url('js/html5.js'); ?>" ></script>';
	<![endif]-->
	<?PHP echo $head; ?>
</head>
<body<?PHP echo body_class(); ?>>
	<div class="page">
	<header class="masthead site-header" role="banner">
		<hgroup>
			<h1 class="site-title"><a href="<?php echo base_url(); ?>" rel="home"><?PHP echo $this->config->item( 'site_title' ); ?></a></h1>
			<h2 class="site-description"><?PHP echo $this->config->item( 'tagline' ); ?></h2>
		</hgroup>
		<nav role="navigation" class="site-navigation main-navigation">
			<!-- <h1 class="assistive-text">Menu</h1> -->
			<!-- <div class="assistive-text skip-link"><a href="#content" title="Skip to content">Skip to content</a></div> -->
			<?php echo $nav['main']; ?>
		</nav><!-- .site-navigation .main-navigation -->
		<div class="top">
			<div class="welcome-message"><?PHP echo $welcome; ?></div>
			<nav role="navigation" class="site-navigation secondary-navigation"><?php echo $nav['secondary']; ?></nav>
		</div>
	</header>
	<div class="login"><?PHP $this->load->view('user/login'); ?></div>
	<div class="upload"><?PHP $this->load->view('photo/upload'); ?></div>
	<div class="site-main">
    <h1 class="page-title"><?PHP echo $title; ?></h1>

    <?PHP if ( $this->session->flashdata('message') ) { ?>
    	<div class="alert"><?php echo $this->session->flashdata('message'); ?></div>
    <?PHP } ?>

    <?PHP if ( $this->session->flashdata('error') ) { ?>
    	<div class="alert alert-error"><?php echo $this->session->flashdata('error'); ?></div>
    <?PHP } ?>

    <?PHP if ( $this->session->flashdata('success') ) { ?>
    	<div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
    <?PHP } ?>

    <?PHP if ( $this->session->flashdata('info') ) { ?>
    	<div class="alert alert-info"><?php echo $this->session->flashdata('info'); ?></div>
    <?PHP } ?>

    <?PHP if ( !empty( $message ) ) { ?>
    	<div class="alert"><?php echo $message; ?></div>
    <?PHP } ?>
    
    <?PHP if ( !empty( $error ) ) { ?>
    	<div class="alert alert-error"><?php echo $error; ?></div>
    <?PHP } ?>

    <?PHP if ( !empty( $success ) ) { ?>
    	<div class="alert alert-success"><?php echo $success; ?></div>
    <?PHP } ?>

    <?PHP if ( !empty( $info ) ) { ?>
    	<div class="alert alert-info"><?php echo $info; ?></div>
    <?PHP } ?>


    <div class="entry-content">
    <div class="pre-content"><?PHP echo $pre_content; ?></div>