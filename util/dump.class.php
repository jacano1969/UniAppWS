<?php 
class Dump {

	public static function html($object){
		print_r('<hr />');
		print_r('DUMPING BEGIN');
		print_r('<hr />');
		foreach($object as $k => $v) {
			print_r('<hr />');
			print_r($k);
			print_r('<hr />');
			print_r($v);
			print_r('<hr />');
			print_r('<hr />');
			print_r('<br />');
			print_r('<br />');
		}
		print_r('<hr />');
		print_r('DUMPING BEGIN');
		print_r('<hr />');
	}

	public static function text($object){
		print_r("==========================\n");
		print_r("DUMPING BEGIN\n");
		print_r("==========================\n");
		foreach($object as $k => $v) {
			print_r("--------------------------\n");
			print_r($k." => ".$v."\n");
			print_r("--------------------------\n");
		}
		print_r("==========================\n");
		print_r("DUMPING END\n");
		print_r("==========================\n");
	}
}
?>
