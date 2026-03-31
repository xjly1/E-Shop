<?php 

if ( ! function_exists('lang'))
{
	function lang($line, $for = '', $attributes = array())
	{
		$translation = get_instance()->lang->line($line);

        if ($translation === FALSE)
        {
            $translation = $line;
        }

		if ($for !== '')
		{
			$translation = '<label for="'.$for.'"'._stringify_attributes($attributes).'>'.$translation.'</label>';
		}

		return $translation;
	}
}