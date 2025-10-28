<?php $this->layout("_login", $front); ?>

<body style="background-color: #666666;">

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" name="login" action="<?= url("login"); ?>" method="post">
					<?= csrf_input(); ?>
					<span class="login100-form-title p-b-43">
						Login
					</span>
					<div class="wrap-input100 validate-input">
						<input class="input-login" type="text" name="email" value="<?= ($cookie ?? null); ?>" placeholder="Email">
					</div>
					<div class="wrap-input100 validate-input">
						<input class="input-login" type="password" name="senha" placeholder="Senha">
					</div>
					<div class="flex-sb-m w-full p-t-3 p-b-32">
						<div class="contact100-form-checkbox">
							<input type="checkbox" id="lembrar" class="checkbox-login" <?= (!empty($cookie) ? "checked" : ""); ?> name="save">
							<label for="lembrar">Lembrar email</label>
						</div>

						<div>
							<a href="<?= url("recuperar"); ?>" class="txt1">
								Esqueceu sua senha?
							</a>
						</div>
					</div>
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div>
					<div class="flex-sb-m w-full p-t-3 p-b-32">
						<div class="container-login100-form-btn">
							<a href="<?= url("cadastro"); ?>" class="login100-form-btn">
								Crie sua conta
							</a>
						</div>
					</div>
				</form>
				<a href="<?= url(); ?>" class="login100-more" style="background-image: url('Source/Images/tflogo.png');">
				</a>
			</div>
		</div>
	</div>
</body>