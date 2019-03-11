<nav class="nav">
      <ul class="nav__list container">
      <?php foreach($categories_rows as $categories_item): ?>
            <li class="nav__item">
                <a href="pages/all-lots.html"><?=$categories_item['name']?></a>
            </li>
            
            <?php endforeach; ?>
      </ul>
    </nav>
    <section class="lot-item container">
      <h2><?= htmlspecialchars($lot['lot_name']); ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="img/<?=$lot['img']; ?>" width="730" height="548" alt="Сноуборд">
          </div>
          <p class="lot-item__category">Категория: <span><?= $lot['category']; ?></span></p>
          <p class="lot-item__description"><?= htmlspecialchars($lot['description']); ?></p>
        </div>
        <div class="lot-item__right">

        <!-- СКРЫТЬ ДЛЯ НЕЗАРЕГИСТРИРОВАННЫХ ЮЗЕРОВ -->
       
          <div class="lot-item__state">
            <div class="lot-item__timer timer">
            <?= time_interval($lot['dt_end']); ?>
              <!-- 10:54 -->
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <?php $current_price = $lot['max_bet']? $lot['max_bet']: $lot['starting_price'] ?>
                <span class="lot-item__cost"><?= price_tag($current_price); ?></span>
              </div>
              <div class="lot-item__min-cost">
              <?php $min_bet = $current_price + $lot['bet_step']; ?>
                Мин. ставка <span><?=price_tag($min_bet);?></span>
              </div>
            </div>
            <?php 
            $bet_2ndtime = false; 
            foreach($bets as $bet) {
              if($bet['user_id'] === $is_auth['id']) { 
                $bet_2ndtime=true;
              } 
            };?>
            <?php
              if(isset($_SESSION['user'])  && $is_auth['id'] != $lot['author_id'] && strtotime(($lot['dt_end'])) > strtotime('now')  && !$bet_2ndtime ): ?>
            <form class="lot-item__form" action="../lot.php?lot_id=<?= ($lot['id'])?>"  method="post">
              <p class="lot-item__form-item form__item form__item--invalid">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="number" name="bet" placeholder="<?=price_tag($min_bet);?>">
                <span class="form__error"><?= $error['bet'] ?? '' ?></span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
            <?php endif; ?>
          </div>
  
              <!-- БЛОК ВЫШЕ СКРЫТ ДЛЯ НЕАВТОРИЗИРОВАННЫХ ЮЗЕРОВ -->
          
          <div class="history">
            <h3>История ставок (<span><?= count($bets) ;?></span>)</h3>
            <table class="history__list">
            <?php foreach($bets as $bet): ?>
              <tr class="history__item">
                <td class="history__name"><?= $bet['user_name'] ;?></td>
                <td class="history__price"><?= $bet['pricetag'] ;?></td>
                <td class="history__time"><?= $bet['dt_add'] ;?></td>
              </tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>

</div>