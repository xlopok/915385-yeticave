<nav class="nav">
      <ul class="nav__list container">
      <?php foreach($categories_rows as $categories_item): ?>
            <li class="nav__item">
                <a href="pages/all-lots.html"><?=$categories_item['name']?></a>
            </li>    
     <?php endforeach; ?>
      </ul>
    </nav>
    <?php $classname = count($errors) ? "form--invalid" : '';?>
    <form class="form container <?=$classname;?>" action="../login.php" method="post"> <!-- form--invalid -->
      <h2>Вход</h2>
      <?php $class_div = isset($errors['email']) ? "form__item--invalid" : '';
      $value = isset($login_form['email']) ? $login_form['email'] : "";
      $error_span = isset($errors['email']) ? $errors['email'] : "";?>
      <div class="form__item <?= $class_div;?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $value;?>">
        <span class="form__error"><?= $error_span;?></span>
      </div>
      <?php $class_div = isset($errors['password']) ? "form__item--invalid" : '';
      $error_span = isset($errors['password']) ? $errors['password'] : "";?>
      <div class="form__item form__item--last <?= $class_div;?>">
        <label for="password">Пароль*</label>
        <input id="password" type="text" name="password" placeholder="Введите пароль" >
        <span class="form__error"><?= $error_span;?></span>
      </div>
      <button type="submit" class="button">Войти</button>
    </form>