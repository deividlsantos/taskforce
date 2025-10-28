<!DOCTYPE HTML>
<html lang="en-US">

<head>
	<!-- Google Tag Manager -->
	<script>
		(function(w, d, s, l, i) {
			w[l] = w[l] || [];
			w[l].push({
				'gtm.start': new Date().getTime(),
				event: 'gtm.js'
			});
			var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s),
				dl = l != 'dataLayer' ? '&l=' + l : '';
			j.async = true;
			j.src =
				'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
			f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer', 'GTM-TLDJRKH3');
	</script>
	<!-- End Google Tag Manager -->

	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TLDJRKH3"
			height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->

	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Home Task Force</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?= url("Source/Images/favicontf.png"); ?>" rel="shortcurt icon" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="<?= url("Source/Views/tcsistemas.home/taskforce/assets/css/bootstrap.min.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?= url("Source/Views/tcsistemas.home/taskforce/venobox/venobox.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?= url("Source/Views/tcsistemas.home/taskforce/assets/css/plugin_theme_css.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?= url("Source/Views/tcsistemas.home/taskforce/style.css?v=") . filemtime("Source/Views/tcsistemas.home/taskforce/style.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?= url("Source/Views/tcsistemas.home/taskforce/assets/css/responsive.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?= url("Source/Views/tcsistemas.home/taskforce/assets/css/loadstyles.css"); ?>">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
	<!-- END HEADER TOP AREA -->
	<div class="tx_top2_relative">
		<div class="">
			<div class="tx_relative_m">
				<div class="">
					<div class="mainmenu_width_tx  ">
						<div class="solutech-main-menu one_page hidden-xs hidden-sm witr_h_h10">
							<div class="solutech_nav_area scroll_fixed postfix">
								<div class="task-header">
									<!-- LOGO -->
									<div class="col-md-3 col-sm-3 col-xs-3">
										<div class="logo">
											<!-- <a class="main_sticky_main_l" href="index.php" title="solutech">
													<img class="taskimg" src="<?= url("Source/Images/tflogop.png"); ?>">
												</a> -->
											<a class="" href="index.php" title="solutech">
												<img class="taskimg" src="<?= url("Source/Images/logoaz.png"); ?>">
											</a>
										</div>
									</div>
									<div class="col-md-9 col-sm-9 col-xs-8">
										<nav class="solutech_menu main-search-menu">
											<?= !empty($hmenu) ? $hmenu : ""; ?>
											<?= !empty($login) ? $login : ""; ?>
											<?= !empty($register) ? $register : ""; ?>
										</nav>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- MOBILE MENU Logo AREA -->
	<div class="mobile_logo_area hidden-md hidden-lg">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="mobile_menu_logo text-center">
						<a href="index.php" title="solutech">
							<img src="<?= url("Source/Images/logoaz.png"); ?>" alt="solutech">
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="register-content">
		<?= $this->section('content'); ?>
	</div>


	<!-- witrfm_footer_area -->
	<div class="witrfm_area">
		<div class="footer-middle">
			<div class="container">
				<div class="row">
					<div class=" col-lg-3 col-md-6 col-sm-12">
						<div class="widget widget_solutech_description_widget">
							<div class="solutech-description-area">
								<section id="con">
									<h2 class="widget-title">Redes sociais</h2>
								</section>
								<p>Siga o Task Force nas redes sociais e fique por dentro de dicas, novidades e atualizações para otimizar a gestão da sua empresa. Acompanhe conteúdos exclusivos sobre produtividade, tutoriais, melhorias no sistema e interaja diretamente com nossa equipe. Não perca nenhuma atualização, siga-nos agora.</p>
								<div class="social-icons">
									<a href="#"><i class="fa fa-facebook-f"></i></a>
									<a href="#"><i class="fab fa-google-plus-g"></i></a>
									<a href="#"><i class="fab fa-x-twitter"></i></a>
									<a href="#"><i class="fas fa-rss"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class=" col-lg-3 col-md-6 col-sm-12">
						<div class="widget widget_solutech_description_widget">
							<h2 class="widget-title">Suporte</h2>
							<p style="color:#fff;">O suporte do Task Force está sempre disponível para ajudar sua empresa a manter a gestão eficiente. Conte com nossa equipe para esclarecer dúvidas, oferecer soluções e garantir o melhor desempenho do sistema. Atendimento ágil e especializado para que você foque no que realmente importa.</p>
						</div>
					</div>

					<div class="col-sm-12 col-md-6  col-lg-3 last">
						<div class="widget_text widget widget_custom_html">
							<h2 class="widget-title">Horário de Funcionamento</h2>
							<div class="textwidget custom-html-widget">
								<div class="witr_table">
									<div class="witr_sub_table">
										<span>Segunda-feira</span>
										<span>08:00 - 18:00</span>
									</div>
									<div class="witr_sub_table">
										<span>Terça-feira</span>
										<span>08:00 - 18:00</span>
									</div>
									<div class="witr_sub_table">
										<span>Quarta-feira</span>
										<span>08:00 - 18:00</span>
									</div>
									<div class="witr_sub_table">
										<span>Quinta-feira</span>
										<span>08:00 - 18:00</span>
									</div>
									<div class="witr_sub_table">
										<span>Sexta-feira</span>
										<span>08:00 - 18:00</span>
									</div>
									<div class="witr_sub_table">
										<span>Emergência:</span>
										<span> +55 17 98816-0666</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-bottom">
			<div class="container">
				<div class="row">
					<div class="col-lg-6 col-md-6  col-sm-12">
						<div class="copy-right-text">
							<p>Copyright &copy; todos os direitos reservados a Task Force.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="ajax_load" style="z-index: 999;">
		<div class="ajax_load_box">
			<div class="ajax_load_box_circle"></div>
			<p class="ajax_load_box_title">Aguarde, carregando...</p>
		</div>
	</div>

	<div class="ajax_response"><?= flash(); ?></div>

	<!-- Include All JS -->
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/vendor/jquery-3.5.1.min.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/bootstrap.min.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/isotope.pkgd.min.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/slick.min.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/imagesloaded.pkgd.min.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/venobox/venobox.min.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/theme-pluginjs.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/jquery.meanmenu.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/ajax-mail.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/map.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/theme.js"); ?>"></script>
	<script src="<?= url("Source/Views/js/jquery.js"); ?>"></script>
	<script src="<?= url("Source/Views/js/jquery.form.js"); ?>"></script>
	<script src="<?= url("Source/Views/js/jquery-ui.js"); ?>"></script>
	<script src="<?= url("Source/Views/tcsistemas.home/taskforce/assets/js/loadscripts.js"); ?>"></script>
</body>

</html>