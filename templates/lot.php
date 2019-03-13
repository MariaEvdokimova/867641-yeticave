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
    <section class="lot-item container">
      <h2><?= isset($lot['lot_name']) ? $lot['lot_name'] : ''; ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?=isset($lot['img_url']) ? $lot['img_url'] : ''; ?>" width="730" height="548" alt="Сноуборд">
          </div>
          <p class="lot-item__category">Категория: <span><?= isset($lot['category_name']) ? $lot['category_name'] : ''; ?></span></p>
          <p class="lot-item__description"><span><?= isset($lot['description']) ? $lot['description'] : ''; ?></p>
        </div>
        <div class="lot-item__right">
          <div class="lot-item__state">
              <?php $end_datetime = isset($lot['end_datetime']) ? $lot['end_datetime'] : '';
              $max_bet = isset($max_bet['max_bet']) ? $max_bet['max_bet'] : 0;
              $start_price = isset($lot['start_price']) ? $lot['start_price'] : 0;
              $value = empty($max_bet) ? $start_price : $max_bet;
              ?>
            <div class="lot-item__timer timer">
                <?=lot_timer(date('d.m.Y',strtotime($end_datetime))); ?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=$value; ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?= isset($lot['step_bet']) ? $lot['step_bet'] : 0; ?></span>
              </div>
            </div>
              <?php if (isset($_SESSION['user'])
              and $_SESSION['user']['id_user'] !== $lot['id_author']
              and $lot['end_datetime'] > date('Y-m-d h:i:s')
              and $user_is_bet === false
              ): ?>
              <form class="lot-item__form" action="/page_content/lot.php?id=<?=$_GET['id']?>" method="post">
                <?php $classname = isset($errors['cost']) ? "form__item--invalid" : "";
                $value = isset($form['cost']) ? $form['cost'] : 0; ?>
              <p class="lot-item__form-item form__item <?=$classname;?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="12 000" value="<?=$value;?>">
                <span class="form__error"><?=$errors['cost'];?></span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
               </form>
              <?php endif; ?>
          </div>
          <div class="history">
            <h3>История ставок (<span><?=count($history_bet);?></span>)</h3>
            <table class="history__list">
                <?php foreach ($history_bet as $value): ?>
                    <tr class="history__item">
                        <td class="history__name"><?=isset($value['name']) ? $value['name'] : '';?></td>
                        <td class="history__price"><?=isset($value['sum_bet']) ? formatting_price($value['sum_bet']) : '';?></td>
                        <td class="history__time"><?=isset($value['creation_date']) ? $value['creation_date'] : '';?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>
