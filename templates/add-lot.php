  <main>
    <nav class="nav">
      <ul class="nav__list container">
          <?php foreach ($categories as $value): ?>
              <li class="nav__item">
                  <a href="all-lots.html"><?=$value['category_name'];?></a>
              </li>
          <?php endforeach; ?>
      </ul>
    </nav>
    <form class="form form--add-lot container form--invalid" action="add.php" method="post" enctype = "multipart/form-data"> <!-- form--invalid -->
      <h2>Добавление лота</h2>
      <div class="form__container-two">
          <?php $classname = isset($errors['lot_name']) ? "form__item--invalid" : "";
          $value = (isset($lot['lot_name']) and $lot['lot_name'] != '') ? $lot['lot_name'] : ""; ?>
        <div class="form__item <?=$classname;?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование</label>
          <input id="lot-name" type="text" name="lot_name" placeholder="Введите наименование лота" value="<?=$value;?>"> <!--required-->
          <span class="form__error">"Введите наименование лота"</span>
        </div>
          <?php $classname = isset($errors['id_category']) ? "form__item--invalid" : "";
            $value = (isset($lot['id_category']) and $lot['id_category'] != '') ? $lot['id_category'] : "";
            $key = array_search($lot['id_category'], array_column($categories, 'id_category'));
            $category = $key ? $categories[$key]['category_name'] : "Выберите категорию";
          ?>
          <div class="form__item <?=$classname;?>">
          <label for="category">Категория</label>
          <select id="category" name="id_category" > <!--required-->
              <option value="<?=$value;?>" ><?=$category?></option>
              <?php foreach ($categories as $value): ?>
                  <option value="<?=$value['id_category'] ?>"><?=$value['category_name'];?></option>
              <?php endforeach; ?>
          </select>
          <span class="form__error"><?=$errors['id_category'];?></span>
        </div>
      </div>
        <?php $classname = isset($errors['description']) ? "form__item--invalid" : "";
        $value = (isset($lot['description']) and $lot['description'] != '') ? $lot['description'] : ""; ?>
        <div class="form__item form__item--wide <?=$classname;?>">
        <label for="message">Описание</label>
        <textarea id="message" name="description" placeholder="Напишите описание лота"><?=$value;?></textarea> <!--required-->
        <span class="form__error">Напишите описание лота</span>
      </div>
        <?php $classname = isset($errors['img_url']) ? "form__item--invalid" : "";
        $value = isset($lot['img_url']) ? $lot['img_url'] : ""; ?>
      <div class="form__item form__item--file <?=$classname;?>"> <!-- form__item--uploaded -->
          <span class="form__error">Загрузите картинку в формате png, jpeg или jpg.</span>
         <label>Изображение</label>
         <div class="preview">
          <button class="preview__remove" type="button">x</button>
          <div class="preview__img">
            <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
          </div>
        </div>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="photo2" src="<?=$value;?>" name="img_url">
          <label for="photo2">
            <span>+ Добавить</span>
          </label>
        </div>
      </div>
      <div class="form__container-three">
          <?php $classname = isset($errors['start_price']) ? "form__item--invalid" : "";
          $value = isset($lot['start_price']) ? $lot['start_price'] : 0; ?>
          <div class="form__item form__item--small <?=$classname;?>">
          <label for="lot-rate">Начальная цена</label>
          <input id="lot-rate" type="text" name="start_price" placeholder="0" value="<?=$value;?>" > <!--required-->
          <span class="form__error">Введите начальную цену > 0</span>
        </div>
          <?php $classname = isset($errors['step_bet']) ? "form__item--invalid" : "";
          $value = isset($lot['step_bet']) ? $lot['step_bet'] : 0; ?>
        <div class="form__item form__item--small <?=$classname;?>">
          <label for="lot-step">Шаг ставки</label>
          <input id="lot-step" type="text" name="step_bet" placeholder="0" value="<?=$value;?>" > <!--required-->
          <span class="form__error">Введите шаг ставки > 0 </span>
        </div>
          <?php $classname = isset($errors['lot_date']) ? "form__item--invalid" : "";
          $value = isset($lot['lot_date']) ? $lot['lot_date'] : "";?>
        <div class="form__item  <?=$classname;?>">
          <label for="lot-date">Дата окончания торгов</label>
          <input class="form__input-date" id="lot-date" type="text" name="lot_date" placeholder="ДД.ММ.ГГГГ" value="<?=$value;?>"> <!--required-->
          <span class="form__error">Введите дату завершения торгов в формате ДД.ММ.ГГГГ</span>
        </div>
      </div>
        <?php if (!empty($errors)): ?>
            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <?php endif; ?>
        <button type="submit" class="button">Добавить лот</button>
    </form>
  </main>
