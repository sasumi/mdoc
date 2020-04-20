<?php
namespace mdoc;

use function LFPhp\Func\dump;

class MyParserDown extends \Parsedown {
	public function title($text){
		$elements = $this->textElements($text);
		foreach($elements as $el){
			if($el['name'] == 'h1'){
				return $this->pureText($el);
			}
		}
		return null;
	}

	public function pureText($element){
		if($element['handler']){
			$els = call_user_func([$this, $element['handler']['function']], $element['handler']['argument']);
			$text = '';
			foreach($els as $el){
				if($el['elements']){
					foreach($el['elements'] as $sub_el){
						$text .= $this->pureText($sub_el);
					}
				} else {
					$text .= $el['text'];
				}
			}
			return $text;
		}
		return $element['text'];
	}
}