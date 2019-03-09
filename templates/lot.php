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
    <section class="lot-item container">
      <h2><?= $lot['lot_name']; ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?= $lot['img_url']; ?>" width="730" height="548" alt="Сноуборд">
          </div>
          <p class="lot-item__category">Категория: <span><?= $lot['category_name']; ?></span></p>
          <p class="lot-item__description"><span><?= $lot['description']; ?></p>
        </div>
        <div class="lot-item__right">
          <?php if (isset($_SESSION['user'])
              and $_SESSION['user']['id_user'] !== $lot['id_author']
              and $lot['end_datetime'] > date('Y-m-d h:i:s')
              and $user_is_bet !== 1
          ): ?>
          <div class="lot-item__state">
            <div class="lot-item__timer timer">
                <?=lot_timer(date('d.m.Y',strtotime($lot['end_datetime']))); ?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?= $lot['start_price']; ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?= $lot['step_bet']; ?></span>
              </div>
            </div>
            <form class="lot-item__form" action="/page_content/lot.php?id=<?=$_GET['id']?>" method="post">
                <?php $classname = isset($errors['cost']) ? "form__item--invalid" : "";
                $value = $form['cost']; ?>
              <p class="lot-item__form-item form__item <?=$classname;?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="12 000" value="<?=$value;?>">
                <span class="form__error"><?=$errors['cost'];?></span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
          </div>
          <?php endif; ?>
        <!--  <div class="history">
            <h3>История ставок (<span>10</span>)</h3>
            <table class="history__list">
              <tr class="history__item">
                <td class="history__name">Иван</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">5 минут назад</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Константин</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">20 минут назад</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Евгений</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">Час назад</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Игорь</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">19.03.17 в 08:21</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Енакентий</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">19.03.17 в 13:20</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Семён</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">19.03.17 в 12:20</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Илья</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">19.03.17 в 10:20</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Енакентий</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">19.03.17 в 13:20</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Семён</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">19.03.17 в 12:20</td>
              </tr>
              <tr class="history__item">
                <td class="history__name">Илья</td>
                <td class="history__price">10 999 р</td>
                <td class="history__time">19.03.17 в 10:20</td>
              </tr>
            </table>
          </div> -->
        </div>
      </div>
    </section>
  </main>
