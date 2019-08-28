<section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach($categories_rows as $categories_item): ?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="pages/all-lots.html"><?=htmlspecialchars($categories_item['name'])?></a>
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
            <?php foreach ($lots_rows as $item): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="img/<?=htmlspecialchars($item['img'])?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=htmlspecialchars($item['category_name'])?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?lot_id=<?= htmlspecialchars($item['id']); ?>"><?=htmlspecialchars($item['lot_name']);?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=price_tag(htmlspecialchars($item['starting_price']));?></span>
                        </div>
                        <div class="lot__timer timer">
                            <?=time_interval(htmlspecialchars($item['dt_end']))?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        <!-- </ul>
        <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
        <li class="pagination-item pagination-item-active"><a>1</a></li>
        <li class="pagination-item"><a href="#">2</a></li>
        <li class="pagination-item"><a href="#">3</a></li>
        <li class="pagination-item"><a href="#">4</a></li>
        <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
      </ul> -->
    </section>