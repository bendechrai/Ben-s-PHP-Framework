<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<title><?php echo htmlspecialchars( Client::GetName() ); ?></title>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo RESOURCE_URL ?>css/style.css"/>
	<!--[if lte IE 6]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo RESOURCE_URL ?>css/ie6.css"/><![endif]-->
	<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="<?php echo RESOURCE_URL ?>css/ie7.css"/><![endif]-->
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo RESOURCE_URL ?>css/calendar.css"/>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo RESOURCE_URL ?>css/dashboard.css"/>
	<script src="http://www.google.com/jsapi" type="text/javascript"></script>
	<script src="<?php echo RESOURCE_URL ?>js/mootools-trunk-1555-compatible.js" type="text/javascript"></script>
	<script src="<?php echo RESOURCE_URL ?>js/common.js" type="text/javascript"></script>
	<script src="<?php echo RESOURCE_URL ?>js/moopop.js" type="text/javascript"></script>
	<script src="<?php echo RESOURCE_URL ?>js/calendar.compat.js" type="text/javascript"></script>
	<script src="<?php echo RESOURCE_URL ?>js/sortableTable.js" type="text/javascript"></script>
	<script language="javascript">
		window.addEvent('load', init );
	</script>
</head>
<body><div id="body">

	<div id="header">
		<h1><?php echo htmlspecialchars( Client::GetName() ); ?></h1>
	</div>

	<div id="nav">
		<ul>
			<!-- list items here for navigation -->
			<?php if( isset($_SESSION['user'] ) && $_SESSION['user'] instanceof User ) : ?>
			<li id="logout"><a href="/logout/">Logout</a></li>
			<?php endif; ?>
		</ul>
	</div>

	<div id="contentwrap"><div id="content">

		<?php if( count( Flash::GetMessages( FLASH::MESSAGE_INFO ) ) > 0 || count( Flash::GetMessages( FLASH::MESSAGE_SUCCESS ) ) > 0 || count( Flash::GetMessages( FLASH::MESSAGE_WARNING ) ) > 0 ) : ?>
		<div id="flash_messages">
			<p class="close">x</p>
			<?php if( count( Flash::GetMessages( FLASH::MESSAGE_INFO ) ) > 0 ) : ?>
				<div id="flash_info">
					<p><?php echo implode( '</p><p>', Flash::GetMessages( FLASH::MESSAGE_INFO ) ) ?></p>
				</div>
			<?php endif; ?>

			<?php if( count( Flash::GetMessages( FLASH::MESSAGE_SUCCESS ) ) > 0 ) : ?>
				<div id="flash_success">
					<p><?php echo implode( '</p><p>', Flash::GetMessages( FLASH::MESSAGE_SUCCESS ) ) ?></p>
				</div>
			<?php endif; ?>

			<?php if( count( Flash::GetMessages( FLASH::MESSAGE_WARNING ) ) > 0 ) : ?>
				<div id="flash_warning">
					<p><?php echo implode( '</p><p>', Flash::GetMessages( FLASH::MESSAGE_WARNING ) ) ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

