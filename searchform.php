<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$sf_id = 'sf-' . wp_unique_id();
?>
<form role="search" method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <label for="<?php echo esc_attr( $sf_id ); ?>" class="screen-reader-text">搜索</label>
  <div class="sf-inner">
    <span class="sf-prompt" aria-hidden="true">$</span>
    <span class="sf-cmd" aria-hidden="true">grep</span>
    <input
      type="search"
      id="<?php echo esc_attr( $sf_id ); ?>"
      class="sf-input"
      name="s"
      value="<?php echo esc_attr( get_search_query() ); ?>"
      placeholder="search ..."
      autocomplete="off"
    >
    <button type="submit" class="sf-submit" aria-label="提交搜索">
      <span aria-hidden="true">↵</span>
    </button>
  </div>
</form>