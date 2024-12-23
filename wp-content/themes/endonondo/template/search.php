<?php
/* Template Name: Search - Keep Out */
get_header();
?>
<style>

form.gsc-search-box {
  margin: 30px 0 ;
}
  .gsc-control-cse {
    font-family: verdana, arial, sans-serif
  }

  .gsc-control-cse .gsc-table-result {
    font-family: verdana, arial, sans-serif
  }

  .gsc-refinementsGradient {
    background: linear-gradient(to left, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0))
  }

  .gsc-control-cse {
    border-color: #FFFFFF;
    background-color: #FFFFFF
  }

  input.gsc-input,
  .gsc-input-box,
  .gsc-input-box-hover,
  .gsc-input-box-focus {
    border: none;
  }

  .gsc-search-button-v2,
  .gsc-search-button-v2:hover,
  .gsc-search-button-v2:focus {
    border-color: #336699;
    background-color: #E9E9E9;
    background-image: none;
    filter: none
  }

  .gsc-search-button-v2 svg {
    fill: #FFFFFF
  }

  .gsc-tabHeader.gsc-tabhActive,
  .gsc-refinementHeader.gsc-refinementhActive {
    color: #CCCCCC;
    border-color: #CCCCCC;
    background-color: #FFFFFF
  }

  .gsc-tabHeader.gsc-tabhInactive,
  .gsc-refinementHeader.gsc-refinementhInactive {
    color: #CCCCCC;
    border-color: #CCCCCC;
    background-color: #FFFFFF
  }

  .gsc-webResult.gsc-result,
  .gsc-results .gsc-imageResult {
    border-color: #FFFFFF;
    background-color: #FFFFFF
  }

  .gsc-webResult.gsc-result:hover {
    border-color: #FFFFFF;
    background-color: #FFFFFF
  }

  .gs-webResult.gs-result a.gs-title:link,
  .gs-webResult.gs-result a.gs-title:link b,
  .gs-imageResult a.gs-title:link,
  .gs-imageResult a.gs-title:link b {
    color: #000000;
    font-size: 22px;
    line-height: 28px;
  }

  .gs-webResult.gs-result a.gs-title:visited,
  .gs-webResult.gs-result a.gs-title:visited b,
  .gs-imageResult a.gs-title:visited,
  .gs-imageResult a.gs-title:visited b {
    color: #000000
  }

  .gs-webResult.gs-result a.gs-title:hover,
  .gs-webResult.gs-result a.gs-title:hover b,
  .gs-imageResult a.gs-title:hover,
  .gs-imageResult a.gs-title:hover b {
    color: #000000
  }

  .gsc-webResult .gsc-url-top {
    padding: 6px 4px;
  }



  .gs-webResult.gs-result a.gs-title:active,
  .gs-webResult.gs-result a.gs-title:active b,
  .gs-imageResult a.gs-title:active,
  .gs-imageResult a.gs-title:active b {
    color: #FF5757
  }


  .gsc-cursor-page {
    color: #FF5757
  }

  a.gsc-trailing-more-results:link {
    color: #FF5757
  }

  .gs-webResult:not(.gs-no-results-result):not(.gs-error-result) .gs-snippet,
  .gs-fileFormatType {
    color: #404040;
    font-size: 16px;
    line-height: 24px;
    font-weight: 400;
  }

  .gs-webResult div.gs-visibleUrl {
    color: #9F9F9F;
    font-size: 14px;
    line-height: 20px;
    font-weight: 400;
  }

  .gs-webResult div.gs-visibleUrl-short {
    color: #9F9F9F
  }

  .gsc-cursor-box {
    border-color: #FFFFFF;
    margin: 30px 0 !important;
    text-align: center;
  }

  .gsc-results .gsc-cursor-box .gsc-cursor-page {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    line-height: 39px;
    text-align: center;
    font-size: 18px;
    color: #B5B7BB;
    display: inline-block;
    margin: 0 6px;
    border: 1px solid #F5F8F7;
    text-decoration: none !important;
  }

  .gsc-results .gsc-cursor-box .gsc-cursor-current-page {
    background: #000000;
    color: #fff;
  }

  .gsc-webResult.gsc-result.gsc-promotion {
    border-color: #336699;
    background-color: #FFFFFF
  }

  .gsc-completion-title {
    color: #FF5757
  }

  .gsc-completion-snippet {
    color: #808184
  }

  .gs-promotion a.gs-title:link,
  .gs-promotion a.gs-title:link *,
  .gs-promotion .gs-snippet a:link {
    color: #0000FF
  }

  .gs-promotion a.gs-title:visited,
  .gs-promotion a.gs-title:visited *,
  .gs-promotion .gs-snippet a:visited {
    color: #663399
  }

  .gs-promotion a.gs-title:hover,
  .gs-promotion a.gs-title:hover *,
  .gs-promotion .gs-snippet a:hover {
    color: #0000FF
  }

  .gs-promotion a.gs-title:active,
  .gs-promotion a.gs-title:active *,
  .gs-promotion .gs-snippet a:active {
    color: #0000FF
  }

  .gs-promotion .gs-snippet,
  .gs-promotion .gs-title .gs-promotion-title-right,
  .gs-promotion .gs-title .gs-promotion-title-right * {
    color: #000000
  }

  .gs-promotion .gs-visibleUrl,
  .gs-promotion .gs-visibleUrl-short {
    color: #008000
  }

  .gcsc-find-more-on-google {
    color: black
  }

  .gcsc-find-more-on-google-magnifier {
    fill: black
  }

  .gsc-modal-background-image-visible {
    display: none
  }

  .gsc-results-wrapper-visible {
    opacity: 1;
    visibility: visible;
    overflow: inherit;
    box-shadow: none !important;
    padding: 0;
    width: auto;
    position: static !important;
  }

  .gsc-overflow-hidden {
    overflow: auto
  }

  .gsc-table-cell-snippet-close,
  .gs-web-image-box,
  .gs-promotion-image-box {
    margin-right: 10px;
  }

  .gsc-control-cse .gsc-option-menu-container {
    width: 120px;
  }

  form.gsc-search-box {
    position: relative;
    margin: 0;
  }

  .gsc-search-box table {
    width: 100% !important;
    border: none;
  }

  button.gsc-search-button {
    position: relative;
    top: 0;
    right: 61px;
    border-radius: 0;
    height: 40px;
    width: 60px;
    background-color: #000000 !important;
    background-image: url(https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/themes/endonondo/assets/images/search.svg) !important;
    background-repeat: no-repeat;
    background-position: center center;
    border: none !important;
    cursor: pointer;
    -webkit-appearance: button;
  }

  .gsc-search-button svg {
    display: none
  }

  form.gsc-search-box input {
    padding-right: 90px !important;
    border: 1px solid #000000 !important;
    height: 41px !important;
    padding: 0 15px !important;
    background: #FFFFFF !important;
  }

  table.gsc-search-box td {
    padding: 0
  }

  .gsib_b {
    display: none
  }

  table.gsc-search-box td.gsc-input {
    padding: 0
  }

  .gcsc-more-maybe-branding-root {
    display: none;
  }
</style>
<main id="content" class="cate-content search-page">
  <div class="page-top-white">
    <div class="container">
      <?php
      if (function_exists('yoast_breadcrumb')) {
        yoast_breadcrumb('<div id="breadcrumbs" class="breacrump">', '</div>');
      }
      ?>
    </div>
  </div>
  <div class="main-search">
    <div class="container">
      <h1 class="text-center">Search</h1>
      <script async src="https://cse.google.com/cse.js?cx=139e6095822144e09"></script>
      <div class="gcse-search"></div>
    </div>
  </div>
</main>
<?php get_footer(); ?>