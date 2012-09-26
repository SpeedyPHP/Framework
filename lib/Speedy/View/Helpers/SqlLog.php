<?php 
namespace Speedy\View\Helpers;


trait SqlLog {
	
	public function printSqlLog() {
		$logger	= \ActiveRecord\Logger\Runtime::instance();
		echo $this->_printLog($logger);
	}
	
	private function _printLog($logger) {
		$html	= 
			'<div class="sql-log">' . 
				"\t<h3>SQL Log</h3>\n" . 
				"\t<table>\n" .
					"\t\t<tr>\n" .
						"\t\t\t<th>Number</th>\n" .
						"\t\t\t<th>Sql</th>\n" .
						"\t\t\t<th>Rows</th>\n" .
						"\t\t\t<th>Time</th>\n" .
					"\t\t</tr>\n";
		
		$i	= 1;
		foreach($logger->get_log() as $log) {
			$html	.=
					"\t\t<tr>\n" .
						"\t\t\t<td>{$i}</td>\n" .
						"\t\t\t<td>{$log['sql']}</td>\n" .
						"\t\t\t<td>{$log['number_rows']}</td>\n" .
						"\t\t\t<td>{$log['execution_time']}</td>\n" . 
					"\t\t</tr>";
			$i++;
		}
		
		$html	.= 	
				"\t</table>\n" . 
			"</div>\n";
		
		return $html;
	}
	
}
?>