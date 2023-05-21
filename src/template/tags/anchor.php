<?php
/**
 *	Anchor TagHandler
 **/

namespace Template\Tags;

class Anchor extends \Template\TagHandler {

	/**
	 * build tag string
	 * @param $attr
	 * @param $content
	 * @return string
	 */
  function build($attr, $content) {
    $is_active = '';

    if (!empty($attr['href'])
      && !preg_match('#^http(s)?://#', $attr['href'])) {

      $check_active = [$attr['href']];
      if (!empty($attr['check-active'])) {
        if ($attr['check-active'] == 'false') {
          $check_active = false;
        }
        $check_active = explode(',', $attr['check-active']);
        unset($attr['check-active']);
      }

      if (is_array($check_active)) {
        $check_active = array_filter($check_active, function($path) {
          if (strpos($path, '{{') !== false) {
            return false;
          }
          return true;
        });
      }

      if ($check_active && count($check_active)) {

        $cond = '(\TemplateHelper::instance()->checkActivePath("'.implode('","',$check_active).'"))';

        if (empty($attr['class'])) {
          $is_active = $this->tmpl->build('{{'.$cond.'?\' class="active"\':\'\'}}');
        }
        else {
          $attr['class'] .= '<?php if '.$cond.' echo " active"; ?>';
        }
      }

      if (!empty($attr['href'])
        && substr($attr['href'], 0, 1) != '/'
        && !preg_match('/\.php$/', $attr['href'])) {
        $attr['data-original-href'] = $attr['href'];
        if (preg_match('/^{{/', $attr['href'])) {
          $attr['href'] = str_replace('}}', '| path }}', $attr['href']);
        }
        else {
          $attr['href'] = \TemplateHelper::instance()->path($attr['href']);
        }
      }
    }

		// resolve all other / unhandled tag attributes
		$attr = $this->resolveParams($attr);
		// create element and return
		return '<a'.$attr.$is_active.'>'.$content.'</a>';
	}

}
