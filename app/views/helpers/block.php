<?php
class BlockHelper extends AppHelper {
    function display($block) {
        $content = '<div class="blue-bg clearfix block-' . $block['Block']['id'] .'"><div class="business-tl"><div class="business-tr"><div class="business-tm">';
        $content .= '<h3>' . $block['Block']['title'] . '</h3>';

        $content .= '</div></div></div>';

        $content .= '    <div class="side1-cl">
      <div class="side1-cr">
        <div class="block1-inner blue-bg-inner clearfix">
          <div class="new-img"></div>
';
        
        $content .= '<p>' . $block['Block']['body'] . '</p>';
        $content .= '</div></div>';
        //$content .= ' <div class="side-bl"><div class="side1-br"><div class="side1-bm"> </div></div></div>';
        $content .= '</div>';
        return $content;
    }
}
