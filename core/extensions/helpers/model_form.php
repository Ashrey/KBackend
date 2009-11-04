<?php


class ModelForm {

	public static function create($model, $action = null) {
	
		$model_name = Util::smallcase(get_class($model));
		if(! $action) $action = ltrim(Router::get('route'),'/');
		
		echo '<form action="',URL_PATH.$action,'" method="post" id="',$model_name,'" class="scaffold">'.PHP_EOL; 
		
		echo '<input id="',$model_name,'_id" name="',$model_name,'[id]" class="id" value="',$model->id.'" type="hidden">'.PHP_EOL; 
		
		$fields = array_diff($model->fields, $model->_at, $model->_in, $model->primary_key);
		
		foreach ($fields as $field){
			
			$tipo = trim(preg_replace('/(\(.*\))/','',$model->_data_type[$field]));//TODO: recoger tamaño y otros valores
			$alias = $model->get_alias($field);
			$formId = $model_name.'_'.$field;
			$formName = $model_name.'['.$field.']';
			
			if (in_array($field, $model->not_null)){
				echo "<label for=\"$formId\" class=\"required\">$alias *</label>".PHP_EOL; 
			} else echo "<label for=\"$formId\">$alias</label>".PHP_EOL; 
			
			switch ($tipo){
						case 'tinyint': case 'smallint': case 'mediumint':
						case 'integer': case 'int': case 'bigint':
						case 'float': case 'double': case 'precision':
						case 'real': case 'decimal': case 'numeric':
						case 'year': case 'day': case 'int unsigned': // Números
							
							echo "<input id=\"$formId\" class=\"int\" name=\"$formName\" type=\"text\" value=\"{$model->$field}\">".PHP_EOL ; 
							break;
						
						case 'datetime': case 'timestamp':
						case 'date': // Usar el js de datetime
							echo "<input id=\"$formId\" class=\"date\" name=\"$formName\" type=\"text\" value=\"{$model->$field}\">".PHP_EOL;
							echo '<script type="text/javascript" src="/javascript/kumbia/jscalendar/calendar.js"></script>
							<script type="text/javascript" src="/javascript/kumbia/jscalendar/calendar-setup.js"></script>
							<script type="text/javascript" src="/javascript/kumbia/jscalendar/calendar-es.js"></script>'.PHP_EOL;
							//echo date_field_tag("$formId");
							break;
						
						case 'enum': case 'set': case 'bool':
						// Intentar usar select y lo mismo para los field_id
							echo "<input id=\"$formId\" class=\"select\" name=\"$formName\" type=\"text\" value=\"{$model->$field}\">".PHP_EOL;
							break;
						
						case 'text': case 'mediumtext': case 'longtext':
						case 'blob': case 'mediumblob': case 'longblob': // Usar textarea
							echo "<textarea id=\"$formId\" class=\"string\" name=\"$formName\">{$model->$field}</textarea>".PHP_EOL;
							break;
						
						default: //text,tinytext,varchar, char,etc se comprobara su tamaño
							echo "<input id=\"$formId\" class=\"string\" name=\"$formName\" type=\"text\" value=\"{$model->$field}\">".PHP_EOL;
							//break;					
			
			}
		}
		//echo radio_field_tag("actuacion", array("U" => "una", "D" => "dos", "N" => "Nada"), "value: N");
		echo '<input type="submit" value="Enviar" />'.PHP_EOL;
		echo '</form>'.PHP_EOL;
	}
}