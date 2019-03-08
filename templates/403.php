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
            <h2>403 Вы не авторизованы</h2>
            <p>Для добавления нового лота, пожалуйста авторизуйтесь.</p>
        </section>
    </main>
