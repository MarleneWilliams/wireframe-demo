<?php
namespace Wiredframe\App;

class WireframesView {

  private $app;
  private $wireframe_model;

  public function __construct(App $app) {
    $this->app = $app;
    $this->wireframe_model = $app->wireframe_model();
  }

  public function index_page() {
    $op = $this->admin_css();
    $op .= $this->static_wireframes_links();
    $op .= $this->dev_wireframes_links();
    return HtmlHelper::theme_admin_page($op);
  }
  public function dev_wireframes_links() {

    $title = 'Development Links';

    $fs_helper = $this->app->fs_helper();
    $cfg = $this->app->config;
    $link_base = '/' . $fs_helper->app_install_path2();

    $wfs = $this->wireframe_model->get_dev_wireframes();
    $wfsop = array();
    foreach($wfs as $wf) {
      $wfsop[] = array(
        'label' => ucwords($wf['wf_key']),
        'url' => $link_base . $wf['wf_key'] . '?' . $cfg->get('dev_key'),
      );
    }
    $op = HtmlHelper::theme_links_list($wfsop);
    $op = '<div class="dev-wireframes-links">' .
          '<h2>' . $title . '</h2>' .
          $op .
          '</div>';
    return $op;
  }

  public function static_wireframes_links() {
    $title = 'Wireframes';
    $wfs = $this->wireframe_model->get_all_static_wireframes();
    $fs_helper = $this->app->fs_helper();

    $url_rel_root = '/' . $fs_helper->app_install_path2() ;

    $cfg = $this->app->config;
    $link_base = '/' . $fs_helper->app_install_path2() . $cfg->get('dev_key') . '/';

    $wfsop = array();
    
    // loop wireframes
    foreach($wfs as $key => $wf) {

      // loop wf versions
      $vmap = $wf['version_map'];
      krsort($vmap);

      $versions_op = array();
      foreach($vmap as $vnum => $vkey) {
        $ver = $wf['versions'][$vkey];

        // mark the current version
        $ver['current'] = $vkey == $wf['cur_ver_key'];
        // prepare version item for theming
        $versions_op[] = array(
          'label' => $this->theme_static_wf_label($ver),
          'url' => $url_rel_root . $key . '?ver=' . $ver['version_num'],
        );
      }

      // theme versions links
      $versions_op = HtmlHelper::theme_links_list($versions_op);
      $versions_op = '<div class="links-list wireframes-links">' .
          $versions_op .
          '</div>';

      // prepare wf item for themeing
      $wf_label = ucwords($key);
      $wf_label = '<a href="' . $url_rel_root . $key . '?ver=' . $wf['cur_ver_num'] . '">' . $wf_label . '</a>
         (v.' . $wf['cur_ver_num'] . ')';
      $wfsop[] = array(
        'label' => $wf_label,
        'items' => $versions_op,
      );
 
    }

    // theme wireframes with versions lists
    $op = '';
    foreach($wfsop as $wfop) {
      $op .= '<li><h3>' . $wfop['label'] . '</h3>' .
              $wfop['items'] .
              '</li>';
    }


    $op = '<div class="static-wireframes-links">
          <h2>' . $title . '</h2>
          <ul>' .
          $op .
          '</ul>
          </div>';
    return $op;
  }

  public function theme_static_wf_label($item) {
    $op = 'Version ' . $item['version_num'];
    if($item['current']) {
      $op .= ' (Current)';
    }
    if($item['name_x']) {
      $op .= ' "' . $item['name_x'] . '"';
    }
    return $op;
  }

  public function admin_css() {
    $op = '<style>
            h2, h3 {
              margin-bottom: 5px;
            }
            ul {
              padding-left:10px;
              margin-top:0;
            }
            ul, li {
              list-style-type: none;
            }
            .static-wireframes-links,
            .dev-wireframes-links,
            .static-wireframes-links > ul > li {
              float:left;
              margin-left: 15px;
            }
            .static-wireframes-links li li {
              font-size:0.8em;
            }
            .dev-wireframes-links {
              clear: both;
            }
            .dev-wireframes-links li {
              display: inline-block;
              margin-right: 15px;
            }


          </style>';
    return $op;
  }
}
