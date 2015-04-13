<?php  

function e($val = '') {
	if (is_array($val) || is_object($val))
		p($val);
	else
		echo sr_($val);
}

function p($val = '') { echo '<pre>'; print_r($val); echo '</pre>'; }

function ex($val = '', $n = 0, $sp = '|') {
	if (empty($val)) {
		return $val;
	} elseif ($n === 'last') {
		$v_ = explode($sp, $val);
		return end($v_);
	} else {
		$v_ = explode($sp, $val);
		if ($n == '-1') {
			return $v_;
		} return !isset($v_[$n]) ? '' : $v_[$n];
	}
}

function sr($a) {
    return str_replace("'", "|+", $a);
}

function sr_($a) {
    return str_replace("|+", "'", $a);
}



?>