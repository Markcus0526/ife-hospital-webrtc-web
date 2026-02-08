<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/
	
	class viewHelper {		
		private $_model;

		function __construct($model) {
			$this->_model = $model;
		}

		private function name_prefix() {
			$prefix = $this->_model->name_prefix;
			if ($prefix)
				return $prefix;
			else
				return "";
		}

		private function to_name($prop) {	
			print ' name="' . $this->name_prefix() . $prop .'"';
		}

		private function to_id($prop) {
			print ' id="' . $this->name_prefix() . $prop .'"';
		}

		private function to_attrs($attr, $other_class=null) {
			if ($attr == null)
				$attr = array();

			print " ";

			if (!isset($attr["class"]) || $attr["class"] == null) 
				$attr["class"] = "";
			if ($other_class != null)
				$attr["class"] .= " " . $other_class;

			foreach($attr as $key => $value)
			{
				if ($key == "readonly") {
					if ($value)
						print $key . " ";
				}
				else if ($key == "disabled") {
					if ($value)
						print $key . " ";
				}
				else
					print $key . "=\"" . $value . "\" ";
			}
		}

		public function input($prop, $attr=null, $decode=false) {
			$val = $this->_model->$prop;
			if ($decode)
				$val = _decode($val);
			?><input type="text" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?> value="<?php p(htmlspecialchars($val)); ?>"><?php
		}

		public function input_number($prop, $attr=null, $decode=false) {
			$val = $this->_model->$prop;
			if ($decode)
				$val = _decode($val);
			?><input type="number" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?> value="<?php p(htmlspecialchars($val)); ?>"><?php
		}

		public function textarea($prop, $rows, $attr=null, $decode=false) {
			$val = $this->_model->$prop;
			if ($decode)
				$val = _decode($val);
			?><textarea <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?> rows="<?php p($rows); ?>"><?php p(htmlspecialchars($val)); ?></textarea><?php
		}

		public function password($prop, $attr=null) {
			?><input type="password" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?> value="<?php p($this->_model->$prop); ?>"><?php
		}

		public function tel($prop, $intl=true, $attr=null) {
			$class = "form-control";
			if ($intl) {
				$class .= " input-intl-tel";
				$type = "input";
			}
			else 
				$type = "tel";
			?><input type="<?php p($type); ?>" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, $class);?> value="<?php p($this->_model->$prop); ?>"><?php
		}

		public function file($prop, $attr=null) {
			?><input type="file" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?> value="<?php p($this->_model->$prop); ?>"><?php
		}

		public function hidden($prop, $attr=null) {
			$val = $this->_model->$prop;
			?><input type="hidden" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr); ?> value="<?php p(htmlspecialchars($val)); ?>"><?php
		}

		public function order_label($field, $label) {
			$ii = "";
			if ($this->_model->sort_field == $field)
			{
				$ii = " <i class='fa fa-chevron-" . ($this->_model->sort_order == "ASC" ? "up" : "down") ."'></i>";
			}
			?><a href="javascript:;" data-sort="<?php p($field); ?>"><?php p($label); ?><?php p($ii);?></a><?php
		}

		public function select_code($prop, $code, $default=null, $attr=null) {
			$codes = _code_labels($code);

			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}
			foreach($codes as $key => $label) {
				$key = $key . "";
				?><option value="<?php p($key); ?>" <?php p($this->_model->$prop == $key ? "selected" : "") ?>><?php p($label) ?></option><?php
			}
			?></select><?php
		}

		public function select2($prop, $prop_text=null, $attr=null) {
			?><input type="text" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control select2");?> value="<?php p($this->_model->$prop); ?>" text="<?php p($this->_model->$prop_text); ?>"><?php
		}

		public function select_dayofweek($prop, $default=null, $attr=null) {
			$this->select_code($prop, CODE_DAYOFWEEK, $default, $attr);
		}

		public function select_dayofmonth($prop, $default=null, $attr=null) {
			$codes = _code_labels($code);

			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}
			for ($m = 1; $m <= 31; $m ++) {
				$m = $m . "";
				?><option value="<?php p($m); ?>" <?php p($this->_model->$prop == $m ? "selected" : "") ?>><?php p($m) ?></option><?php
			}
			?></select><label><?php l("日"); ?></label><?php
		}

		public function select_year($prop, $min, $max, $default=null, $attr=null) {
			$codes = _code_labels($code);

			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}
			for ($y = $min; $y <= $max; $y ++) {
				$y = $y . "";
				?><option value="<?php p($y); ?>" <?php p($this->_model->$prop == $y ? "selected" : "") ?>><?php p($y) ?></option><?php
			}
			?></select><?php
		}

		public function select_month($prop, $default=null, $attr=null) {
			$codes = _code_labels($code);

			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}
			for ($m = 1; $m <= 12; $m ++) {
				$m = $m . "";
				?><option value="<?php p($m); ?>" <?php p($this->_model->$prop == $m ? "selected" : "") ?>><?php p($m) ?></option><?php
			}
			?></select><?php
		}

		public function select_model($prop, $model, $val_field, $text_field, $default=null, $sqloption=null, $attr=null) {
			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}	
			$where = "";
			if ($sqloption != null) {
				if ($sqloption["order"] != null)
					$order = $sqloption["order"];
				else
					$order = "create_time ASC";
				if ($sqloption["where"] != null)
					$where = $sqloption["where"];
			}
			$err = $model->select($where, array("order" => $order));
			while ($err == ERR_OK)
			{
				$key = $key . "";
				?><option value="<?php p($model->$val_field); ?>" <?php p($this->_model->$prop === $model->$val_field ? "selected" : "") ?>><?php p($model->$text_field) ?></option><?php
				$err = $model->fetch();
			}
			?></select><?php
		}

		public function select_depart($prop, $default=null, $attr=null) {
			$model = new departModel;
			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}	
			$where = "";
			$order = "sort ASC";
			$err = $model->select($where, array("order" => $order));
			while ($err == ERR_OK)
			{
				$key = $key . "";
				?><option value="<?php p($model->depart_id); ?>" <?php p($this->_model->$prop === $model->depart_id ? "selected" : "") ?>>
					<?php if($model->depth == 2) p("&nbsp;&nbsp;&nbsp;"); p($model->depart_name) ?>
				</option><?php
				$err = $model->fetch();
			}
			?></select><?php
		}

		public function select_times($prop, $prop_start, $prop_end, $attr=null) {
			if ($this->_model->$prop_start == "")
				$start = "";
			else {
				$start = _time($this->_model->$prop_start);
				$times = $start;
			}
			if ($this->_model->$prop_end == "")
				$end = "";
			else {
				$end = _time($this->_model->$prop_end);
				$times .= "-" . $end;
			}
			?>
			<input type="text" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr); ?> value="<?php p($times); ?>" readonly>
			<input type="hidden" id="<?php p($prop_start); ?>" name="<?php p($prop_start); ?>" value="<?php p($start); ?>">
			<input type="hidden" id="<?php p($prop_end); ?>" name="<?php p($prop_end); ?>" value="<?php p($end); ?>"> 

			<?php
		}

		public function select_utype($prop, $default=null, $attr=null) {
			$codes = _code_labels(CODE_UTYPE);
			$my_type = _my_type();

			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}
			foreach($codes as $key => $label) {
				if ($key == UTYPE_NONE)
					continue;
				if ($my_type == UTYPE_SUPER || $key > $my_type) {
					$key = $key . "";
					?><option value="<?php p($key); ?>" <?php p($this->_model->$prop == $key ? "selected" : "") ?>><?php p($label) ?></option><?php
				}
			}
			?></select><?php
		}

		public function select_utype_code($prop, $code, $default=null, $attr=null) {
			$codes = _code_labels(CODE_ENABLE);
			$my_type = _my_type();

			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}
			$v = $this->_model->$prop & $code ? "1" : "0";
			foreach($codes as $key => $label) {
				if ($my_type == UTYPE_SUPER) {
					$key = $key . "";
					?><option value="<?php p($key); ?>" <?php p($key == $v ? "selected" : "") ?>><?php p($label) ?></option><?php
				}
			}
			?></select><?php
		}

		public function select_user($prop, $default=null, $attr=null) {
			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}	
			$user = new userModel;
			$where = "";
			$order = "user_name ASC";
			$err = $user->select($where, array("order" => $order));
			while ($err == ERR_OK)
			{
				$key = $key . "";
				?><option value="<?php p($user->user_id); ?>" <?php p($this->_model->$prop === $user->user_id ? "selected" : "") ?>><?php p($user->user_name) ?></option><?php
				$err = $user->fetch();
			}
			?></select><?php
		}

		public function select_phrase($prop, $phtype_id, $default=null, $attr=null) {
			$phrases = phraseModel::get_all_phrases($phtype_id);

			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}
			foreach($phrases as $i => $phrase) {
				?><option value="<?php p($phrase["phrase_code"]); ?>" <?php p($this->_model->$prop == $phrase["phrase_code"] ? "selected" : "") ?>><?php p($phrase["content"]) ?></option><?php
			}
			?></select><?php
		}

		public function select_disease($prop, $default=null, $attr=null) {
			global $g_diseases;
			if ($g_diseases == null) {
				$g_diseases = diseaseModel::get_all();
			}

			?><select <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?>><?php
			if ($default != null && $default != "")
			{
				?><option value="" <?php p($this->_model->$prop == null ? "selected" : "") ?>><?php p($default) ?></option><?php
			}
			foreach($g_diseases as $disease) {
				$disease_id = $disease["disease_id"];
				$disease_name = $disease["disease_name"];
				?><option value="<?php p($disease_id); ?>" <?php p($this->_model->$prop == $disease_id ? "selected" : "") ?>><?php p($disease_name) ?></option><?php
			}
			?></select><?php
		}

		public function input_user($prop_id, $prop_name, $readonly=false) {
			$this->hidden($prop_id);
			$this->input($prop_name, array("class" => "input", "readonly" => "readonly"));
			if (!$readonly) {
				?>&nbsp;<a href="users/select_user" class="btn select-user fancybox" fancy-width="900" fancy-height="600"><div>…</div></a><?php
			}
		}

		public function radio($prop, $code, $attr=null) {
			$codes = _code_labels($code);
			$rand_id = rand(1, 100);

			?><div class="radio-list"><?php
			foreach($codes as $key => $label) {
				$id = $prop . "_" . $key . "_" . $rand_id;
				?><label class="ui-radio" for="<?php p($id); ?>"><input type="radio" class="radio" <?php $this->to_id($id); $this->to_name($prop); $this->to_attrs($attr);?> value="<?php p($key); ?>" <?php p($this->_model->$prop == $key ? "checked=true" : "");?>><span><?php p($label) ?></span></label><?php
			}
			?></div><?php
		}

		public function radio_single($prop, $label, $key, $attr=null) {
			$id = $prop . "_" . $key;
			?><div class="radio-list"><label class="ui-radio" for="<?php p($id); ?>"> <input type="radio" class="radio" <?php $this->to_id($id); $this->to_name($prop); $this->to_attrs($attr);?> value="<?php p($key); ?>" <?php p($this->_model->$prop == $key ? "checked=true" : "");?>><span><?php p($label) ?></span></label></div><?php
		}

		public function checkbox($prop, $code, $attr=null, $vertical=false, $add_all=false, $all_label="") {
			$codes = _code_labels($code);
			$rand_id = rand(1, 100);

			$val = $this->_model->$prop;
			if ($val == null)
				$val = array();
			else if (!is_array($val)) {
				$val = _bits2arr($val);
			}

			?><div class="checkbox-list <?php if($vertical) p("vertical"); ?>">
			<input type="checkbox" <?php $this->to_name($prop . "[]");?> value="-1" checked class="input-null">
			<?php

			if ($add_all) {
				$id = $prop . "_all";
				$checked = true;
				$all_class = "there-is-all";
				foreach($codes as $key => $label) {
					if (!in_array($key, $val))
					{
						$checked = false;
						break;
					}
				}
			?>
				<label class="ui-checkbox" for="<?php p($id); ?>"><input type="checkbox" class="checkbox" id="<?php p($id); ?>" group="<?php p($prop);?>" <?php $this->to_name($prop . "_all");?> value="all" <?php if($checked) {?>checked=true <?php } $this->to_attrs($attr);?>><span><?php p($all_label) ?></span></label>
			<?php
			}

			foreach($codes as $key => $label) {
				$id = $prop . "_" . $key . "_" . $rand_id;
				$checked = in_array($key, $val);
				?><label class="ui-checkbox" for="<?php p($id); ?>"><input type="checkbox" class="checkbox <?php p($all_class); ?>"  id="<?php p($id); ?>" <?php $this->to_name($prop . "[]");?> value="<?php p($key); ?>" <?php if($checked) {?>checked=true <?php } $this->to_attrs($attr);?>><span><?php p($label) ?></span></label><?php
			}
			?></div><?php
		}

		public function checkbox_single($prop, $label, $attr=null) {
			$val = $this->_model->$prop;
			$name = $prop . "_@@@[]";
			?><div class="checkbox-list"><label class="ui-checkbox" for="<?php p($prop); ?>"> <input type="checkbox" id="<?php p($prop); ?>" <?php $this->to_name($name);?> value="1" <?php p($val == 1 ? "checked=true" : ""); $this->to_attrs($attr);?>><span><?php p($label) ?></span></label><input type="checkbox" <?php $this->to_name($name);?> value="-1" checked class="input-null"></div><?php
		}

		public function checkbox_only($prop, $attr=null) {
			if (isset($attr["id"]))
				$id = $attr["id"];
			else
				$id = $prop;

			$title = isset($attr["title"]) ? $attr["title"] : "";
			?><label class="ui-checkbox checkbox-only" for="<?php p($id); ?>" title="<?php p($title); ?>"> <input type="checkbox" id="<?php p($id); ?>" <?php $this->to_name($prop);?> value="1" <?php p($this->_model->$prop == 1 ? "checked=true" : ""); $this->to_attrs($attr);?>><span></span></label><?php
		}

		public function checkbox_switch($prop, $attr=null) {
			$val = $this->_model->$prop;
			?>
			<input id="<?php p($prop); ?>" name="<?php p($prop); ?>" type="checkbox" <?php p($val == 1 ? "checked=true" : ""); $this->to_attrs($attr, "make-switch");?> data-size="small"><?php
		}

		public function checkbox_join($prop, $code, $attr=null) {
			$codes = _code_labels($code);
			$vals = preg_split("/,/", $this->_model->$prop);

			foreach($codes as $key => $label) {
				$id = $prop . "_" . $key;
				$checked = "";
				foreach($vals as $val) {
					if ($key == $val) {
						$checked = "checked=true";
						break;
					}
				}
				?><label class="checkbox inline" for="<?php p($id); ?>"><input type="checkbox" class="checkbox"  id="<?php p($id); ?>" <?php $this->to_name($prop . "[]");?> value="<?php p($key); ?>" <?php p($checked); $this->to_attrs($attr);?>><?php p($label) ?></label><?php
			}
		}

		public function checkbox_language($prop, $attr=null, $vertical=false) {
			global $g_languages;
			if ($g_languages == null) {
				$g_languages = languageModel::get_all();
			}
			$rand_id = rand(1, 100);

			?><div class="checkbox-list <?php if($vertical) p("vertical"); ?>">
			<input type="checkbox" <?php $this->to_name($prop . "[]");?> value="-1" checked class="input-null"><?php

			$val = $this->_model->$prop;
			if ($val == null)
				$val = array();
			foreach($g_languages as $language) {
				$language_id = $language["language_id"];
				$language_name = $language["language_name"];

				$id = $prop . "_" . $language_id . "_" . $rand_id;
				$checked = in_array($language_id, $val);
				?><label class="ui-checkbox" for="<?php p($id); ?>"><input type="checkbox" class="checkbox"  id="<?php p($id); ?>" <?php $this->to_name($prop . "[]");?> value="<?php p($language_id); ?>" <?php if($checked) {?>checked=true <?php } $this->to_attrs($attr);?>><span><?php p($language_name) ?></span></label><?php
			}
			?></div><?php
		}

		public function checkbox_disease($prop, $attr=null, $vertical=false) {
			global $g_diseases;
			if ($g_diseases == null) {
				$g_diseases = diseaseModel::get_all();
			}

			?><div class="checkbox-list <?php if($vertical) p("vertical"); ?>">
			<input type="checkbox" <?php $this->to_name($prop . "[]");?> value="-1" checked class="input-null"><?php

			$val = $this->_model->$prop;
			if ($val == null)
				$val = array();
			foreach($g_diseases as $disease) {
				$disease_id = $disease["disease_id"];
				$disease_name = $disease["disease_name"];

				$id = $prop . "_" . $disease_id;
				$checked = in_array($disease_id, $val);
				?><label class="ui-checkbox" for="<?php p($id); ?>"><input type="checkbox" class="checkbox"  id="<?php p($id); ?>" <?php $this->to_name($prop . "[]");?> value="<?php p($disease_id); ?>" <?php if($checked) {?>checked=true <?php } $this->to_attrs($attr);?>><span><?php p($disease_name) ?></span></label><?php
			}
			?></div><?php
		}

		public function detail($prop, $format="%s") {
			$s = sprintf($format, $this->_model->$prop);
			if($s == "")
				$s = "&nbsp;";
			p($s);
		}

		public function detail_del($prop, $format="%s") {
			$s = sprintf($format, $this->_model->$prop);
			if($s == 1)
				$s = "删除";
			else 
				$s = "";
			p($s);
		}

		public function installed($prop) {
			if ($this->_model->$prop)
				l("已安装");
			else
				l("未安装");

			$this->input($prop, array("class" => "input-null"));
		}

		public function nl2br($prop, $format="%s") {
			$s = sprintf($format, $this->_model->$prop);
			$s = _str2html($s);
			if($s == "")
				$s = "&nbsp;";
			p($s);
		}

		public function html($prop) {
			$html = $this->_model->$prop;

			$s = 0; 
			$e = 0;
			do {
				$e = strpos($html, "[mod]", $s);

				if ($e === false) {
					print substr($html, $s);
					break;
				}
				if ($e > 0)
					print substr($html, $s, ($e - $s));

				$e2 = strpos($html, "[/mod]", $e);
				if ($e2 === false) {
					print substr($html, $e);
					break;
				}

				$mod_path = substr($html, $e + 5/*[mod]*/, $e2 - $e - 5);
				module::shortcode($mod_path);

				if ($e2 > 0)
					$e2 += 6; // [/mod]

				$s = $e2;
			}
			while(true);
		}

		public function detail_html($prop) {
			$s = sprintf("%s", $this->_model->$prop);
			$s = nl2br($s);
			$s = str_replace("\\n", "", $s);
			p($s);
		}

		public function number($prop, $default="&nbsp;") {
			$s = number_format($this->_model->$prop);
			if($s == "")
				$s = $default;
			p($s);
		}

		public function currency($prop, $critical=0) {
			$v = $this->_model->$prop;
			$s = _currency($v);
			if($s == "")
				$s = "&nbsp;";
			else {
				$s = "<i class='fa fa-yen'></i> " . $s;
			}
			p($s);
		}

		public function paragraph($prop) {
			if ($this->_model->$prop == "")
				p("&nbsp;");
			else
				p(_str2paragraph($this->_model->$prop));
		}

		public function summary($prop) {
			if ($this->_model->$prop == "")
				p("&nbsp;");
			else
				p(_str2firstparagraph($this->_model->$prop));
		}

		public function dateinput($prop, $attr=null) {
			if ($this->_model->$prop == null)
				$s = "";
			else 
				$s = _date($this->_model->$prop);
			?><input type="text" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?> value="<?php p($s); ?>"><?php
		}

		public function datebox($prop, $class=null, $attr=null) {
			if ($this->_model->$prop == null)
				$s = "";
			else 
				$s = _date($this->_model->$prop);

			$dp_attr = array();
			if ($attr && isset($attr["data-date-start-date"])) {
				$dp_attr["data-date-start-date"] = $attr["data-date-start-date"];
			}
			if ($attr && isset($attr["data-date-end-date"])) {
				$dp_attr["data-date-end-date"] = $attr["data-date-end-date"];
			}
			?>
			<div class="input-group date date-picker <?php p($class); ?>" data-date-format="yyyy/mm/dd" <?php $this->to_attrs($dp_attr);?>>
				<input type="text" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "form-control");?> value="<?php p($s); ?>">
				<span class="input-group-btn">
					<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
				</span>
			</div><?php
		}

		public function timebox($prop, $attr=null) {
			if ($this->_model->$prop == null)
				$s = "";
			else 
				$s = _time($this->_model->$prop);
			?><input type="text" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "input-xmini");?> value="<?php p($s); ?>" data-mask="99:99" data-mask-placeholder= "-"><?php
			?><label class="fa fa-clock mark-calendar"></label><?php
		}

		public function date($prop) {
			if ($this->_model->$prop == null)
				$s = "&nbsp;";
			else {
				$s = _date($this->_model->$prop);
				if($s == "")
					$s = "&nbsp;";
			}
			p($s);
		}

		public function datetime($prop) {
			if ($this->_model->$prop == null)
				$s = "&nbsp;";
			else {
				$s = _date_time($this->_model->$prop);
				if($s == "")
					$s = "&nbsp;";
			}
			p($s);
		}

		public function time($prop) {
			if ($this->_model->$prop == null)
				$s = "&nbsp;";
			else {
				$s = _time($this->_model->$prop);
				if($s == "")
					$s = "&nbsp;";
			}
			p($s);
		}

		public function times($prop1, $prop2) {
			if ($this->_model->$prop1 == null || $this->_model->$prop2 == null)
				$s = "&nbsp;";
			else {
				$s = _DateTime($this->_model->$prop2)->getTimestamp() - _DateTime($this->_model->$prop1)->getTimestamp();
				if ($s <= 0) 
					$s = "";
				$s = sprintf("%02d:%02d", $s / 3600, ($s % 3600) / 60);
				if($s == "")
					$s = "&nbsp;";
			}
			p($s);
		}

		public function seconds($prop) {
			p(_seconds_label($this->_model->$prop));
		}

		public function detail_code($prop, $code) {
			$codes = _code_labels($code);
			$s = "";
			if(isset($codes[$this->_model->$prop]))
				$s = $codes[$this->_model->$prop];
			if($s == "")
				$s = "&nbsp;";
			p($s);
		}

		public function detail_code_multi($prop, $code, $active_code = null) {
			$codes = _code_labels($code);

			$s = "";
			foreach($codes as $key => $label) {
				if ($this->_model->$prop & $key) 
				{
					if ($s != "") $s.=", ";
					if ($key == $active_code) {
						$s .= "<span class='label label-important'>" . $label . "</span>";
					}
					else {
						$s .= $label;
					}
				}
			}
			p($s);
		}

		public function detail_code_multi_join($prop, $code, $active_code = null) {
			$codes = _code_labels($code);

			$s = "";
			$vals = preg_split("/,/", $this->_model->$prop);
			foreach($codes as $key => $label) {
				foreach($vals as $val) 
				{
					if ($val == $key) {
						if ($s != "") $s.=", ";
						if ($key == $active_code) {
							$s .= "<span class='label label-important'>" . $label . "</span>";
						}
						else {
							$s .= $label;
						}
					}
				}
			}
			p($s);
		}

		public function code($prop, $code) {
			$codes = _code_labels($code);
			p($codes[$this->_model->$prop]);
		}

		public function autobox($prop, $attr=null) {
			if ($attr["class"] == null)
				$attr["class"] = "input-medium";
			$attr["class"] .= " auto-complete";

			?><input type="text" <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr);?> value="<?php p($this->_model->$prop); ?>"><?php
		}

		public function tags($prop, $max=null) {
			$tags = $this->_model->$prop;

			foreach($tags as $i => $tag)
			{
				if ($max > 0 && $i >= $max)
					break;

				if ($i > 0) {
					?>, <?php
				} 

				?><a href="blog/tag/<?php p($tag);?>"><?php p($tag);?></a><?php
			}
		}

		public function attach_down($prop1, $prop2) {
			$file_name= $this->_model->$prop1;
			$path = $this->_model->$prop2;
			?><a href="common/down/<?php p($path);?>/<?php p($file_name);?>"> <?php p($file_name);?> 
				</a> <?php
			
		}

		public function input_tags($prop, $attr=null) {
			if (is_array($this->_model->$prop))
				$tags = join(',', $this->_model->$prop);
			else
				$tags = "";
			?><input type="text" <?php $this->to_attrs($attr, "tagsinput"); $this->to_id($prop); $this->to_name($prop); ?> value="<?php p($tags); ?>" data-role="tagsinput"><?php
		}

		public function photo_url($prop) {
			p(_photo_url($this->_model->$prop, $this->_model->ext));
		}

		public function thumb_url($prop) {
			p(_photo_url($this->_model->$prop, "png"));
		}

		public function detail_utype($prop, $no_label=false) {
			$utype = $this->_model->$prop;
			switch($utype) {
				case UTYPE_SUPER:
					$label = "label-danger";
					break;
				case UTYPE_ADMIN:
					$label = "label-warning";
					break;
				case UTYPE_PATIENT:
					$label = "label-info";
					break;
				case UTYPE_DOCTOR:
					$label = "label-default";
					break;
			}

			if ($no_label == false) {
			?><span class="label <?php p($label); ?>"><?php 
			}
			$this->detail_code($prop, CODE_UTYPE);
			if ($no_label == false) {
			?></span><?php
			}
		}

		public function detail_sex($prop) {
			$sex = $this->_model->$prop;
			switch($sex) {
				case SEX_MAN:
					$label = "label-important";
					$icon = "fa fa-male";
					break;
				case SEX_WOMAN:
					$label = "label-warning";
					$icon = "fa fa-female";
					break;
			}
			?><i class="<?php p($icon); ?>"></i> <?php $this->detail_code($prop, CODE_SEX);?><?php
		}

		public function editor($prop, $attr=null, $editor_type = EDITORTYPE_INLINE) {
			?><textarea <?php $this->to_id($prop); $this->to_name($prop); $this->to_attrs($attr, "cke_textarea"); ?>  rows="<?php p($editor_type == EDITORTYPE_INLINE ? "1" : "50"); ?>"><?php
			p(htmlentities($this->_model->$prop, ENT_QUOTES));
			?></textarea><?php
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/