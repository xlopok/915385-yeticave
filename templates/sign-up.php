<nav class="nav">
    <ul class="nav__list container">
    <?php foreach($categories_rows as $categories_item): ?>
            <li class="nav__item">
                <a href="pages/all-lots.html"><?=htmlspecialchars($categories_item['name'])?></a>
            </li>    
     <?php endforeach; ?>
    </ul>
</nav>
<?php $classname = count($errors) ? "form--invalid" : "";?>
<form class="form container <?= $classname ;?>" action="../sign-up.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Регистрация нового аккаунта</h2>
    <?php $classname_div = isset($errors['email']) ? "form__item--invalid" : "";
          $value = isset($reg_form['email']) ? $reg_form['email'] : "";
          $error_span = isset($errors['email']) ? $errors['email'] : "";
    ?>
    <div class="form__item <?=$classname_div; ?>"> <!-- form__item--invalid -->
    <label for="email">E-mail*</label>
    <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= htmlspecialchars($value); ?>" >
    <span class="form__error"><?=$error_span; ?></span>
    </div>
    <?php $classname_div = isset($errors['password']) ? "form__item--invalid" : "";
          $value = isset($reg_form['password']) ? $reg_form['password'] : "";
          $error_span = isset($errors['password']) ? $errors['password'] : "";
    ?>
    <div class="form__item <?=$classname_div; ?>">
    <label for="password">Пароль*</label>
    <input id="password" type="text" name="password" placeholder="Введите пароль" >
    <span class="form__error"><?=$error_span; ?></span>
    </div>
    <?php $classname_div = isset($errors['name']) ? "form__item--invalid" : "";
          $value = isset($reg_form['name']) ? $reg_form['name'] : "";
          $error_span = isset($errors['name']) ? $errors['name'] : "";
    ?>
    <div class="form__item <?=$classname_div; ?>">
    <label for="name">Имя*</label>
    <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= htmlspecialchars($value); ?>">
    <span class="form__error"><?=$error_span; ?></span>
    </div>
    <?php $classname_div = isset($errors['message']) ? "form__item--invalid" : "";
          $value = isset($reg_form['message']) ? $reg_form['message'] : "";
          $error_span = isset($errors['message']) ? $errors['message'] : "";
    ?>
    <div class="form__item <?=$classname_div; ?>">
    <label for="message">Контактные данные*</label>
    <textarea id="message" name="message" placeholder="Напишите как с вами связаться" ><?= htmlspecialchars($value); ?></textarea>
    <span class="form__error"><?=$error_span; ?></span>
    </div>

    <?php $classname_div = isset($errors['avatar']) ? "form__item--invalid" : "";
          $error_span= isset($errors['avatar']) ? $errors['avatar'] : "";
    ?>
    <div class="form__item form__item--file form__item--last <?= $classname_div; ?>">
    <label>Аватар</label>
    <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
        <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
        </div>
    </div>
    <div class="form__input-file">
        <input class="visually-hidden" type="file" id="photo2" name="avatar" value="">
        <label for="photo2">
        <span>+ Добавить</span>
        </label>
        <span class="form__error" style="width:400px; margin-top:-5px;"><?= $error_span;?></span>
    </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="login.php">Уже есть аккаунт</a>
</form>