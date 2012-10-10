<?php 
namespace Speedy\Traits;

trait Text {

	public function summarize($content, $more = '...', $strip_tags = true, $allowable_tags = '', $maxLength = 200) {
		if ($strip_tags) 
			$content = strip_tags($content, $allowable_tags);
		
		if (strlen($content) < $maxLength) 
			return $content;
		
		$testPos = strpos($content, ' ');
		if ($testPos === false || $testPos >= $maxLength) 
			return substr($content, 0, $maxLength) . $more;

		$aContent = explode(' ', $content);
		$summary	= '';
		while (($part = array_shift($aContent)) && (strlen($summary) + strlen($part) + 1) < $maxLength) {
			if (strlen($summary) > 0)
				$summary .= ' ';
			$summary .= $part;
		}
		
		return $summary;
	}
	
}
?>