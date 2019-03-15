  <main>
    <nav class="nav">
      <ul class="nav__list container">
          <?php foreach ($categories as $value): ?>
              <li class="nav__item">
                  <a href="/page_content/all-lots.php?id=<?=$value['id_category'];?>"><?=isset($value['category_name']) ? $value['category_name'] : '';?></a>
              </li>
          <?php endforeach; ?>
      </ul>
    </nav>
    <form class="form container" action="sign-up.php" method="post" enctype = "multipart/form-data"> <!-- form--invalid -->
      <h2>Регистрация нового аккаунта</h2>
        <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
        $value = (isset($sign['email']) and $sign['email'] != '') ? $sign['email'] : ""; ?>
      <div class="form__item <?=$classname;?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$value;?>">
        <span class="form__error"><?=$errors['email'];?></span>
      </div>
        <?php $classname = isset($errors['password']) ? "form__item--invalid" : "";
        $value = (isset($sign['password']) and $sign['password'] != '') ? $sign['password'] : ""; ?>
      <div class="form__item <?=$classname;?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?=$value;?>">
        <span class="form__error">Введите пароль</span>
      </div>
        <?php $classname = isset($errors['name']) ? "form__item--invalid" : "";
        $value = (isset($sign['name']) and $sign['name'] != '') ? $sign['name'] : ""; ?>
      <div class="form__item <?=$classname;?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?=$value;?>">
        <span class="form__error"><?=$errors['name'];?></span>
      </div>
        <?php $classname = isset($errors['contacts']) ? "form__item--invalid" : "";
        $value = (isset($sign['contacts']) and $sign['contacts'] != '') ? $sign['contacts'] : ""; ?>
      <div class="form__item <?=$classname;?>">
        <label for="message">Контактные данные*</label>
        <textarea id="message" name="contacts" placeholder="Напишите как с вами связаться"><?=$value;?></textarea>
        <span class="form__error"><?=$errors['contacts'];?></span>
      </div>
        <?php $classname = isset($errors['avatar']) ? "form__item--invalid" : "";
        $value = isset($sign['avatar']) ? $sign['avatar'] : ""; ?>
      <div class="form__item form__item--file form__item--last <?=$classname;?>">
        <span class="form__error"><?=$errors['avatar'];?></span>
        <label>Аватар</label>
        <div class="preview">
          <button class="preview__remove" type="button">x</button>
          <div class="preview__img">
            <img src="/img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
          </div>
        </div>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="photo2" src="<?=$value;?>" name="avatar">
          <label for="photo2">
            <span>+ Добавить</span>
          </label>
        </div>
      </div>
        <?php if (isset($errors)): ?>
            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <?php endif; ?>
      <button type="submit" class="button">Зарегистрироваться</button>
      <a class="text-link" href="#">Уже есть аккаунт</a>
    </form>
  </main>
