<?php echo $this->extend('templates/admin') ?>
<?php echo $this->section('content') ?>
<section class="register-admin">
    <a name="start"></a>
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-lg-6 offset-lg-3 mt-5 mb-5 pt-3 pb-3 bg-white form-wrapper">
                <div class="container">
                    <h3>Регистрация администратора</h3>
                    <!--Можем вывести сразу все сообщения об ошибках(взято из документации), но будем делать это для каждого поля в отдельности при помощи нашего кастомного хелпера Form -->
                    <!-- validation_list_errors() -->
                    <hr>
                    <!-- Выводим в браузер сообщения об успехе или неудаче регистрации -->
                    <div id="register-admin-result" class="text-center"></div>

                    <div class="form-group">
                        <label for="login">Логин</label>
                        <input id="new_admin_login" type="text" class="form-control" name="login" placeholder="Введите логин" value="<?= set_value('name') ?>">
                        <div id="login-admin-valid"></div>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input id="new_admin_password" type="password" class="form-control" name="password" placeholder="Введите пароль" value="">
                        <div id="password-admin-valid"></div>
                    </div>
                    <div class="form-group">
                        <label for="password">Подтвердить пароль</label>
                        <input id="new_admin_cpassword" type="password" class="form-control" name="cpassword" placeholder="Введите пароль повторно" value="">
                        <div id="cpassword-admin-valid"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 col-sm-4">
                            <button id="new_admin_button" type="submit" class="btn btn-primary" name="btn" value="new_admin">Зарегистрировать администратора</button>
                        </div>
                        <div class="col-12 col-sm-8 d-flex justify-content-center align-items-center border">
                            <a class="text-right border" href="<?= site_url('/dimon') ?>">Вернуться в админпанель</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<?php echo $this->endSection() ?>