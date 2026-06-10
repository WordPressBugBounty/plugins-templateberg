<?php defined( 'ABSPATH' ) or die; ?>
<div class="tb-accordion__wrap">
    <h3>
        <?php
        esc_attr_e('Frequently Asked Questions', 'templateberg');
        ?>
    </h3>
    <?php
    $templateberg_faq = templateberg_connect()->faq();
    foreach ($templateberg_faq as $templateberg_key => $templateberg_setting) {
        echo "<div class='tb-accordion'>";
        echo "<div class='tb-accordion__header'>";
        echo "<h4 class='tb-accordion__heading'>";
        echo "<a data-toggle='collapse' href='#tb-faq__" . esc_attr($templateberg_key) . "'>";
        echo '<span>';
        echo esc_html($templateberg_setting['q']);
        echo '</span>';
        echo '</a>';
        echo '</h4>';
        echo '</div>';
        echo "<div id='tb-faq__" . esc_attr($templateberg_key) . "' class='hidden tb-accordion__bodywrap'>";
        echo "<div class='tb-accordion__body'>";
        echo wp_kses_post($templateberg_setting['a']);
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    ?>
</div>
