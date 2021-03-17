<html>
<style>
  .my-error {
    color: red;
    font-style: italic;
  }
</style>
	<form action="">
		 <section class="login-content">
           <div class="container h-100">
              <div class="row justify-content-center align-items-center height-self-center">
                 <div class="col-md-5 col-sm-12 col-12 align-self-center">
                    <div class="sign-user_card">
                       <div class="logo-detail">            
                             <div class="d-flex align-items-center"> <h4 class="logo-title ml-3">Ту_Душка</h4></div>
                       </div>  
                       <h3 class="mb-2">Зарегистрироваться</h3>
                       <p>Создай свой аккаунт</p>
                       <form action="#">
                          <div class="row" id="s1">
                             <div class="col-lg-6">
                                <div class="floating-label form-group">
                                   <input id="firstname" class="floating-input form-control" type="text" placeholder="Имя">
                                   <div class="my-error" id="error-firstname"></div>
                                </div>
                             </div>
                             
                             <div class="col-lg-6">
                                <div class="floating-label form-group">
                                   <input id="lastname" class="floating-input form-control" type="text" placeholder="Фамилия">
                                </div>
                             </div>
                            
                             <div class="col-lg-12">
                                <div class="floating-label form-group">
                                   <input id="phone" class="floating-input form-control" type="text" placeholder="Телефон в формате +78001001010">
                                   <div class="my-error" id="error-phone"></div>
                                </div>
                             </div>
                             
                             <div class="col-lg-6">
                                <div class="floating-label form-group">
                                   <input id="password" class="floating-input form-control" type="password" placeholder="Пароль">
                                 	<div class="my-error" id="error-password"></div>
                                </div>
                             </div>
                             
                             <div class="col-lg-6">
                                <div class="floating-label form-group">
                                   <input id="re_password" class="floating-input form-control" type="password" placeholder="Повтори пароль">
                                   <div class="my-error" id="error-re_password"></div>
                                </div>
                             </div>
                             
                             <div class="col-lg-12">
                                <div class="custom-control custom-checkbox mb-3 text-left">
                                   <input id="customCheck1" type="checkbox" name="checkbox" class="custom-control-input" value="">
                                   <label class="custom-control-label" for="customCheck1">Я согласен с пользовательским соглашением</label>
                                   <div class="my-error" id="error-checkbox"></div>
                                </div>
                             </div>
                             <!--<div class="g-recaptcha" data-sitekey="6LfDzX0aAAAAAEHlspKjdFAwe2oT_DiEz7FLfSIv"></div>
                             	<?php foreach ($errors['g-recaptcha-response'] as $value):?>
	                          		<div class="my-error" id="error-g-recaptcha-response"><?php echo $value; ?></div>
	                          	<?php endforeach; ?>
	                          <br/>-->
                         
                          
                          <button name="reg-submit" class="btn btn-primary">Зарегистрироваться</button>
                        </div>
                        <div id="s2" style="display: none;">
                          <div class="floating-label form-group">
                            <input class="cr-round--lg" type="text" id="code" placeholder="Введите последние 6-ть цифр номера со входящего звонка">
                            <div id="error-code" name="errors"></div>
                          </div>
                          <div class="floating-label form-group">
                          <button id="reg_finish" class="food__btn"><span>Завершить регистрацию</span></button>
                        </div>
                          
                       </form>
                    </div>
                 </div>
              </div>
           </div>
        </section>
	</form>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script type="text/javascript">
		$("[name=reg-submit]").click(function (e) {
			e.preventDefault();
			
			$.ajax({
				url: "/?section=reg",
				method: "POST",
				data: {
					"firstname": $("#firstname").val(),
					"lastname": $("#lastname").val(),
					"phone": $("#phone").val(),
					"password": $("#password").val(),
					"re_password": $("#re_password").val(),
          "checkbox": $('#customCheck1').is(':checked')
				}
			}).done(function (r) {
				if (r.data !== undefined) {
          $("#s1").hide(200);
          $("#s2").show(200);
        } else if (r.errors !== undefined) {
          $(".my-error").html("");
          for(key in r.errors) {
            $("#error-"+key).html(r.errors[key]);
          }
        }
			});
		});
		
	</script>
</html>