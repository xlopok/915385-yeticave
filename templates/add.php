<nav class="nav">
      <ul class="nav__list container">
      <?php foreach($categories_rows as $categories_item): ?>
            <li class="nav__item">
                <a href="pages/all-lots.html"><?=$categories_item['name']?></a>
            </li>
            
            <?php endforeach; ?>
      </ul>
    </nav>
    <?php $classname = count($errors) ? "form--invalid" : "";?>
    <form class="form form--add-lot container <?= $classname ;?>" action="../add.php" method="post"  enctype="multipart/form-data"> <!-- form--invalid -->
      <h2>Добавление лота</h2>
      <div class="form__container-two">

      <?php $classname_div = isset($errors['lot-name']) ? "form__item--invalid" : "";
          $value = isset($lot['lot-name']) ? $lot['lot-name'] : "";
       ?>
        <div class="form__item <?=$classname_div; ?>"> <!--  -->
          <label for="lot-name">Наименование</label>
          <input id="lot-name" type="text" value="<?= $value ;?>" name="lot-name" placeholder="Введите наименование лота" >
          <?php if (isset($errors['lot-name'])): ?>
          <span class="form__error"><?=$errors['lot-name']; ?></span>
          <?php endif; ?>
        </div>
        <?php $classname_div = isset($errors['category']) ? "form__item--invalid" : "";
          $value = isset($lot['category']) ? $lot['category'] : "";
       ?>
        <div class="form__item <?=$classname_div; ?>">
          <label for="category">Категория</label>
          <select id="category" name="category" required>
            <option>Выберите категорию</option>
            <?php foreach($categories_rows as $categories_item): ?>
            <option value="<?=$categories_item['id'] ?>" <?php $sel = ($value ==  $categories_item['id']) ? 'selected' : "";?> <?=$sel; ?>>
              <?=$categories_item['name'];?>
            </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['category'])): ?>
          <span class="form__error"><?=$errors['category']; ?></span>
          <?php endif; ?>
        </div>
      </div>
      <?php $classname_div = isset($errors['message']) ? "form__item--invalid" : "";
          $value = isset($lot['message']) ? $lot['message'] : "";
          $error_span= isset($errors['message']) ? $errors['message'] : "";
       ?>
      <div class="form__item form__item--wide <?= $classname_div;?>"> <!-- form__item--invalid -->
        <label for="message">Описание</label>
        <textarea id="message" name="message" placeholder="Напишите описание лота" ><?= $value ;?></textarea>
        <span class="form__error"><?=$error_span;?></span>
      </div>
      <?php $classname_div = isset($errors['lot-photo']) ? "form__item--invalid" : "";
          // $value = isset($lot['message']) ? $lot['message'] : "";
          $error_span= isset($errors['lot-photo']) ? $errors['lot-photo'] : "";
       ?>
      <div class="form__item form__item--file <?= $classname_div; ?>">   <!--form__item--uploaded -->
        <label>Изображение</label>
        <div class="preview">
          <button class="preview__remove" type="button">x</button>
          <div class="preview__img">
            <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
          </div>
        </div>
        <div class="form__input-file">
        
          <input class="visually-hidden" name="lot-photo" type="file" id="photo2" value="">
          <label for="photo2">
            <span>+ Добавить</span>
          </label>
          <span class="form__error" style="width:400px; margin-top:-5px;"><?= $error_span;?></span>
        </div>
      </div>
      <div class="form__container-three">
      <?php $classname_div = isset($errors['lot-rate']) ? "form__item--invalid" : "";
          $value = isset($lot['lot-rate']) ? $lot['lot-rate'] : "";
          $error_span= isset($errors['lot-rate']) ? $errors['lot-rate'] : "";
       ?>
        <div class="form__item form__item--small <?= $classname_div;?>"> <!-- form__item--invalid -->
          <label for="lot-rate">Начальная цена</label>
          <input id="lot-rate" type="number" name="lot-rate" placeholder="0" value="<?= $value;?>" >
          <span class="form__error"><?= $error_span;?></span>
        </div>
        <?php $classname_div = isset($errors['lot-step']) ? "form__item--invalid" : "";
          $value = isset($lot['lot-step']) ? $lot['lot-step'] : "";
          $error_span= isset($errors['lot-step']) ? $errors['lot-step'] : "";
        ?>
        <div class="form__item form__item--small <?= $classname_div;?>">
          <label for="lot-step">Шаг ставки</label>
          <input id="lot-step" type="number" name="lot-step" placeholder="0" value="<?= $value;?>">
          <span class="form__error"><?=  $error_span;?></span>
        </div>
        <?php $classname_div = isset($errors['lot-date']) ? "form__item--invalid" : "";
          $value = isset($lot['lot-date']) ? $lot['lot-date'] : "";
          $error_span= isset($errors['lot-date']) ? $errors['lot-date'] : "";
       ?>
        <div class="form__item <?= $classname_div;?>">
          <label for="lot-date">Дата окончания торгов</label>
          <input class="form__input-date" id="lot-date" type="date" name="lot-date" value="<?= $value;?>">
          <span class="form__error"><?=  $error_span;?></span>
        </div>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button">Добавить лот</button>
    </form>