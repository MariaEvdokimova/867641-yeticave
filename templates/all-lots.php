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
    <div class="container">
      <section class="lots">
          <?php $first = reset($lots);
          $category_name = isset($first['category_name']) ? $first['category_name'] : 0;
          ?>
        <h2>Все лоты в категории <span>«<?=$category_name;?>»</span></h2>
        <ul class="lots__list">
            <?php foreach ($lots as $value): ?>
              <li class="lots__item lot">
                <div class="lot__image">
                  <img src="<?=isset($value['img_url']) ? $value['img_url'] : ''; ?>" width="350" height="260" alt="Сноуборд">
                </div>
                  <?=$category = isset($categories[$value['category_name']]) ? $categories[$value['category_name']] : '';
                  $id_lot = isset($value['id_lot']) ? $value['id_lot'] : 0;
                  $lot_name = isset($value['lot_name']) ? htmlspecialchars($value['lot_name']) : '';
                  $start_price = isset($value['start_price']) ? formatting_price($value['start_price']) : '';
                  $end_datetime = isset($value['end_datetime']) ? $value['end_datetime'] : '';
                  ?>
                <div class="lot__info">
                  <span class="lot__category"><?=$category; ?></span>
                  <h3 class="lot__title"><a class="text-link" href="/page_content/lot.php?id=<?=$id_lot;?>"><?=$lot_name; ?></a></h3>
                  <div class="lot__state">
                    <div class="lot__rate">
                      <span class="lot__amount">Стартовая цена</span>
                      <span class="lot__cost"><?=$start_price; ?></span>
                    </div>
                    <div class="lot__timer timer">
                        <?=lot_timer(date('d.m.Y',strtotime($end_datetime))); ?>
                    </div>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
        </ul>
      </section>
        <?php $first = reset($lots);
            $id_category = isset($first['id_category']) ? $first['id_category'] : 0;
        ?>
        <?= include_template('pagination.php', [
            'pages' => $pages,
            'pages_count' => $pages_count,
            'cur_page' => $cur_page,
            'url' => '/page_content/all-lots.php?id=' . $id_category
        ]); ?>
    </div>
  </main>
