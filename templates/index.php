<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <!--заполните этот список из массива категорий-->
        <?php foreach ($categories as $value): ?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="pages/all-lots.html"><?=$value['category_name'];?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <!--заполните этот список из массива с товарами-->
        <?php foreach ($announcement_list as $value): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$value['img_url']; ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$categories[$value['category_name']]; ?></span>
                    <h3 class="lot__title"><a class="text-link" href="/page_content/lot.php?id=<?=$value['id_lot'];?>"><?=htmlspecialchars($value['lot_name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=formatting_price($value['start_price']); ?><!--<b class="rub">р</b>--></span>
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