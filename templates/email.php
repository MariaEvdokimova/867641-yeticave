<?php
$winner_name = isset($winners['name']) ? $winners['name'] : '';
$id_lot = isset($winners['id_lot']) ? $winners['id_lot'] : 0;
$lot_name = isset($winners['lot_name']) ? $winners['lot_name'] : '';
$id_user = isset($winners['id_user']) ? $winners['id_user'] : 0;
?>
<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?=$winner_name;?></p>
<p>Ваша ставка для лота <a href="http://867641-yeticave-master/page_content/lot.php?id=<?=$id_lot;?>"><?=$lot_name;?></a> победила.</p>
<p>Перейдите по ссылке <a href="http://867641-yeticave-master/page_content/my-lots.php?id=<?=$id_user;?>">мои ставки</a>,
    чтобы связаться с автором объявления</p>

<small>Интернет Аукцион "YetiCave"</small>