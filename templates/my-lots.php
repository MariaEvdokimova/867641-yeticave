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
    <section class="rates container">
      <h2>Мои ставки</h2>
      <table class="rates__list">
          <?php foreach ($lots as $value): ?>
          <?php
              $id_lot = isset($value['id_lot']) ? $value['id_lot'] : '';
              $lot_name = isset($value['lot_name']) ? $value['lot_name'] : '';
              $category_name = isset($value['category_name']) ? $value['category_name'] : '';
              $end_datetime = isset($value['end_datetime']) ? $value['end_datetime'] : '';
              $sum_bet = isset($value['sum_bet']) ? $value['sum_bet'] : '';
              $creation_date = isset($value['creation_date']) ? formatting_price($value['creation_date']) : '';
              $img_url = isset($value['img_url']) ? $value['img_url'] : '';
              $id_winner = isset($value['id_winner']) ? $value['id_winner'] : 0;

              $classname = '';
              if(!empty($id_winner)){
                  $classname = "timer--win";
                  $end_datetime = 'Ставка выиграла';
              }elseif(strtotime($end_datetime) <= time()){
                  $classname = "timer--end";
                  $tr_classname = "rates__item--end";
                  $end_datetime = 'Торги окончены';
              }elseif ((strtotime($end_datetime) - time()) < 86400){
                  $classname = "timer--finishing";
                  $end_datetime = lot_timer(date('d.m.Y',strtotime($end_datetime)));
              }else{
                  $end_datetime = lot_timer(date('d.m.Y',strtotime($end_datetime)));
              };

              ?>
        <tr class="rates__item">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?=$img_url;?>" width="54" height="40" alt="<?=$lot_name; ?>">
            </div>
            <h3 class="rates__title"><a href="/page_content/lot.php?id=<?=$id_lot;?>"><?=$lot_name; ?></a></h3>
          </td>
          <td class="rates__category">
              <?=$category_name; ?>
          </td>
          <td class="rates__timer <?=$tr_classname;?>">
            <div class="timer <?=$classname;?>"><?=$end_datetime; ?></div>
          </td>
          <td class="rates__price">
            <?=$sum_bet;?>
          </td>
          <td class="rates__time">
              <?=$creation_date;?>
          </td>
        </tr>
          <?php endforeach; ?>
      </table>
    </section>
  </main>
