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
                <span class="lot-item__cost"><?= $lot['start_price'] + $max_bet['max_bet']; ?></span>
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
          <div class="history">
            <h3>История ставок (<span><?=count($history_bet);?></span>)</h3>
            <table class="history__list">
                <?php foreach ($history_bet as $value): ?>
                    <tr class="history__item">
                        <td class="history__name"><?=$value['name'];?></td>
                        <td class="history__price"><?=formatting_price($value['sum_bet']);?></td>
                        <td class="history__time"><?=$value['date_bet'];?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>
