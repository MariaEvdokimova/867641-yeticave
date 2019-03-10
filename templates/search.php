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
    <div class="container">
      <section class="lots">
        <h2>Результаты поиска по запросу «<span><?=$search;?></span>»</h2>
        <ul class="lots__list">
            <?php foreach ($lots as $value): ?>
          <li class="lots__item lot">
            <div class="lot__image">
              <img src="<?=$value['img_url']; ?>" width="350" height="260" alt="Сноуборд">
            </div>
            <div class="lot__info">
              <span class="lot__category"><?=$categories[$value['category_name']]; ?></span>
              <h3 class="lot__title"><a class="text-link" href="/page_content/lot.php?id=<?=$value['id_lot'];?>"><?=htmlspecialchars($value['lot_name']); ?></a></h3>
              <div class="lot__state">
                <div class="lot__rate">
                  <span class="lot__amount">Стартовая цена</span>
                  <span class="lot__cost"><?=formatting_price($value['start_price']); ?></span>
                </div>
                <div class="lot__timer timer">
                    <?=lot_timer(date('d.m.Y',strtotime($value['end_datetime']))); ?>
                </div>
              </div>
            </div>
          </li>
            <?php endforeach; ?>
        </ul>
      </section>
      <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
        <li class="pagination-item pagination-item-active"><a>1</a></li>
        <li class="pagination-item"><a href="#">2</a></li>
        <li class="pagination-item"><a href="#">3</a></li>
        <li class="pagination-item"><a href="#">4</a></li>
        <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
      </ul>
    </div>
  </main>
