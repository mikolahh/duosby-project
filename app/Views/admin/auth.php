<?php echo $this->extend('templates/admin') ?>

<?php echo $this->section('content') ?>

<section class="auth">
    <a name="start"></a>
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-lg-6 offset-lg-3 mt-5 mb-5 pt-3 pb-3 bg-white form-wrapper">
                <div class="container">
                    <h3>Вход в админпанель</h3>
                    <hr>
                    <form action="<?= base_url('admin/auth') ?>" method="post" autocomplete="off">
                        <!-- Выводим в браузер скрытое поле -->
                        <?= csrf_field() ?>

                        <!-- Выводим в браузер сообщения об успехе или неудаче регистрации -->
                        <?php if (!empty(session()->getFlashdata('fail'))) : ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('fail') ?></div>
                        <?php endif ?>

                        <?php if (!empty(session()->getFlashdata('success'))) : ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif ?>

                        <div class="form-group">
                            <label for="name">Логин</label>
                            <input type="text" class="form-control" name="name" value="<?= set_value('name') ?>">
                            <span class="text-danger"><?= isset($validation_object) ? display_error($validation_object, 'name') : '' ?></span>
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль</label>
                            <input type="password" class="form-control" name="password" value="">
                            <span class="text-danger"><?= isset($validation_object) ? display_error($validation_object, 'password') : '' ?></span>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 col-sm-4">
                                <button type="submit" class="btn btn-primary">Войти</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php echo $this->endSection() ?>