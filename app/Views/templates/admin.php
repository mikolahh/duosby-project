<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="description" content="<?= $description ?? '' ?>" />

    <link href="https://fonts.googleapis.com/css2?family=Montserrat+Alternates:wght@700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Raleway:wght@300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <title><?= $title ?? '' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css.map') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
</head>
<body>
    <header class="header-admin">
        <div class="container">
            <div class="header-admin-offer">
                <span class="header-admin-title">DuoS.by - панель администратора</span>
                <ul class="header-admin-list">
                    <li>
                        <a href="<?=site_url()?>">Главная</a> 
                    </li>
                    <li>
                        <a href="<?=site_url('admin/registerduosadmin')?>">Регистрация админа</a>
                    </li>
                </ul>
            </div>            
        </div>
    </header>

    <!-- Content -->
    <?php $this->renderSection('content'); ?>

    <footer>
      
    </footer>

    <script src="<?= base_url('assets/js/jquery-3.6.4.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/slick.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>

</html>