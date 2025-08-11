<?php

if (constant('B_PROLOG_INCLUDED') !== true) {
    die();
}

/**
 * @var array $arResult
 */

?>
<div class="brand-promo-wrapper">
    <section class="brand-promo-section">
        <div class="brand-promo-section__menu-and-slider">
            <div class="brand-promo-section__slider">
                <div class="brand-promo-slider">
                    <div class="brand-promo-slider__slides">
                        <?php
                        for ($i = 0; $i <= count($arResult['PROMO']['BANNERS']['PICTURE']) - 1; $i++):
                            $srcset = "{$arResult['PROMO']['BANNERS']['PICTURE'][$i]} 1x";
                            $srcset .= ", {$arResult['RESIZER']->resize($arResult['PROMO']['BANNERS']['PICTURE'][$i], 2046, 1000)} 2x"
                        ?>
                        <div class="brand-promo-slider__slide-wrapper">
                            <a href="<?= $arResult['PROMO']['BANNERS']['LINK'][$i] ?>" class="brand-promo-slider__slide">
                                <img srcset="<?= $srcset; ?>" src="<?= $arResult['PROMO']['BANNERS']['PICTURE'][$i] ?>" alt="" class="brand-promo-slider__img"/>
                            </a>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    foreach (array_reverse($arResult['PROMO']['GRID']['CHILD']) as $section) :
        if (empty($section['SECTION_NAME'])) {
            continue;
        }
    ?>
        <section class="brand-promo-section">
            <div class="brand-promo-section__container">
                <h2 class="brand-promo-section__title h2_mb27"><a href="<?= $section['LINK_SECTION'] ?>" class="bem-link"><?= $section['SECTION_NAME'] ?></a></h2>
                <div class="brand-promo-grid brand-promo-grid_hidden">
                    <?php for ($i = 0; $i <= count($section['PICTURE']) - 1; $i++) : ?>
                        <a href="<?= $section['LINK'][$i] ?>" class="brand-promo-grid__item">
                            <div class="brand-promo-grid__wrap-img">
                                <img srcset="<?= $section['PICTURE'][$i] ?> 1x, <?= $section['PICTURE'][$i] ?> 2x" src="<?= $section['PICTURE'][$i] ?>" alt="" class="brand-promo-grid__img">
                            </div>
                            <h4 class="brand-promo-grid__title"><?= $section['NAME'][$i] ?></h4>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="brand-promo-section__container">

                <?php
                $out = '';

                ob_start();
                for ($i = 0; $i <= count($section['PICTURE_DESCRIPTION']) - 1; $i++) :
                ?>
                    <div class="brand-promo-blocks__block brand-promo-blocks__block_content">
                        <div class="brand-promo-blocks__wrap-content">
                            <h3 class="brand-promo-blocks__title"><?= $section['NAME_DESCRIPTION'][$i] ?></h3>
                            <p class="brand-promo-blocks__desc"><?= $section['DESCRIPTION'][$i] ?></p>
                            <a href="<?= $section['LINK_DESCRIPTION'][$i] ?>" class="brand-promo-blocks__link">К моделям этой серии »</a>
                        </div>
                    </div>

                    <?php
                    $tmp = @ob_get_contents() ?: '';
                    ob_clean();
                    ?>
                    <div class="brand-promo-blocks__block">
                        <div class="brand-promo-blocks__wrap-img">
                            <img src="<?= $section['PICTURE_DESCRIPTION'][$i] ?>" alt="" class="brand-promo-blocks__img">
                        </div>
                    </div>

                <?php
                    $t = @ob_get_contents() ?: '';
                    if (($i % 2) == 0) {
                        $tmp .= $t;
                    } else {
                        $tmp = $t . $tmp;
                    }

                    ob_clean();

                    $out .= "<div class=\"brand-promo-blocks\">{$tmp}</div>";
                endfor;
                ob_end_clean();

                echo $out;
                ?>

            </div>
        </section>
    <?php endforeach; ?>
    <section class="brand-promo-section">
        <div class="brand-promo-section__container">
            <div class="brand-promo-video">
                <?php for ($i = 0; $i <= count($arResult['PROMO']['VIDEOS']['LINK']) - 1; $i++) : ?>
                    <div class="brand-promo-video__item">
                        <iframe width="560" height="315" src="<?= $arResult['PROMO']['VIDEOS']['LINK'][$i] ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>
</div>