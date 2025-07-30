<?php
use App\DBConnection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Translate a string using pre-defined terms.
 *
 * @param $string
 * @return mixed
 */
function translate($string)
{
	$user = \Auth::user();

	if (! $user)
		return $string;

	$language = \Auth::user()->language();

	if (! $language)
		return $string;

	if ($language->code == 'en')
		return $string;

	$languageHelper = new \App\Language;
	$terms = $languageHelper->getTerms($language->code);

	foreach ($terms as $original => $translated)
	{
		if ($string == $original && $translated)
			return $translated;
	}

	return $string;
}

/**
 * Replace custom field tag with an actual value.
 *
 * @param $assignment_id
 * @param $string
 * @return mixed
 */
function custom_fields($assignment_id, $string)
{
	$assignment = \App\Assignment::find($assignment_id);

	if (! $assignment)
		return $string;

	$assessment = \App\Assessment::find($assignment->assessment_id);

	if (! $assessment)
		return $string;

	// Job name custom field
	if ($assignment->job_id)
		$string = str_replace('[job]', $assignment->job->name, $string);

	if (! $assessment->use_custom_fields)
		return $string;

	if (! $assignment->custom_fields)
		return $string;

	// Find the role index
	$roleIndex = null;
	foreach ($assignment->custom_fields['type'] as $typeId => $type)
	{
		if ($type == 'role')
		{
			$roleIndex = $typeId;
			break;
		}
	}

	// Find the name index
	$nameIndex = null;
	foreach ($assignment->custom_fields['type'] as $typeId => $type)
	{
		if ($type == 'name')
		{
			$nameIndex = $typeId;
			break;
		}
	}

	foreach ($assessment->custom_fields['tag'] as $i => $custom_field)
	{
		// Highjack the name custom field to be yourself when role is self
		if ($roleIndex !== null and $nameIndex !== null and strtolower($assignment->custom_fields['value'][$roleIndex]) == 'self')
			$string = str_replace('[name]', 'yourself', $string);

		// Replace custom field tag with its correct value
		$string = str_replace('['.$custom_field.']', $assignment->custom_fields['value'][$i], $string);
	}

	return $string;
}

/**
 * Initialize SSE.
 */
function sse_init()
{
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	sse_send(0, 0);
}

/**
 * Send some data using SSE.
 *
 * @param $iteration
 * @param $message
 */
function sse_send($iteration, $message)
{
	$data = [
		'i' => $iteration,
		'message' => $message
	];
	echo "data: " . json_encode($data) . PHP_EOL;
	echo PHP_EOL;

	ob_flush();
	flush();
}

/**
 * Finish sending data and return it.
 *
 * @param $data
 */
function sse_complete($data)
{
	sse_send(-1, $data);
}

/**
 * Get the average of all the values in the array.
 *
 * @param $array
 * @return float|void
 */
function array_average($array)
{
	if (! is_array($array))
		return;

	return array_sum($array) / count($array);
}

/**
 * Check if a certain string contains a specific word.
 *
 * @param $string
 * @param $word
 * @return bool
 */
function contains_word($string, $word)
{
	if (strpos(strtolower($string), strtolower($word)) !== false) {
		return true;
	}

	return false;
}

/**
 * Replace shortcodes in a string with the passed data.
 *
 * @param $shortcodes
 * @param $subject
 * @return String
 */
function do_shortcodes($shortcodes, $subject)
{
	foreach ($shortcodes as $shortcode => $content)
		$subject = str_replace('['.$shortcode.']', $content, $subject);

	return $subject;
}

/**
 * Format a collection into an array for a select element.
 *
 * @param Collection $collection
 * @return array
 */
function get_select_formatted_array(Collection $collection)
{
	$array = [];
	foreach ($collection as $item)
		$array[$item->id] = $item->name;

	return $array;
}

/**
 * Get the default text for an email body sent out to users.
 *
 * @return string
 */
function get_default_email_body()
{
	return '<p>Hello, <b>[name]</b></p>
		<p>
			You have been assigned to complete the following assessments:<br/>
			[assessments]
		</p>
		<p>These assignments will expire on <b>[expiration-date]</b>.</p>
		<p>
			Login here to view your assignments:<br/>
			[login-link]<br/>
			<br/>
			You can use the following credentials to log in:<br/>
			username: <i>[username]</i><br/>
			password: <i>[password]</i>
		</p>
		<p><i>Please Note: If you already logged in before and changed your password, use the new password you have set for yourself instead.</i></p>
		<p>&copy; '.date('Y').' AOE Science</p>';
}

/**
 * Get the path where uploaded images are stored.
 *
 * @return string
 */
function uploads_path()
{
	return public_path() . '/uploads';
}

/**
 * Get an image with its correct uploads path, unless an absolute url.
 *
 * @param $image
 * @return string
 */
function show_image($image)
{
	return (substr($image, 0, 4) === 'http' ? $image : '/uploads/'.$image);
}

/**
 * Return a string lower-cased, with spaces as underscores and all special
 * characters removed.
 *
 * @param $string
 * @param null $replaceSpacesWith
 * @return mixed
 */
function clean_string($string, $replaceSpacesWith = null)
{
	$string = preg_replace("/[^ \w]+/", "", $string);
	$string = strtolower($string);
	$string = str_replace(' ', ($replaceSpacesWith ? $replaceSpacesWith : '_'), $string);

	return $string;
}

/**
 * Return a string with special characters removed.
 *
 * @param $string
 * @return mixed
 */
function sanitize_string($string)
{
	$string = preg_replace("/[^ \w]+/", "", $string);

	return $string;
}

/**
 * Return a string capitalized, with dashes and underscores replaced with spaces.
 *
 * @param $string
 * @return string
 */
function readable_string($string)
{
	$string = str_replace('-', ' ', $string);
	$string = str_replace('_', ' ', $string);
	$string = ucwords($string);

	return $string;
}

/**
 * Return a boolean string value for the integer passed in.
 * @param $int
 * @return string
 */
function int_to_boolean_string($int)
{
	if ($int)
		return 'True';

	return 'False';
}

function xml_to_json($xml)
{
	$root = (func_num_args() > 1 ? false : true);
	$jsnode = [];

	if (!$root)
	{
		if (count($xml->attributes()) > 0)
		{
			$jsnode["$"] = [];
			foreach($xml->attributes() as $key => $value)
				$jsnode["$"][$key] = (string)$value;
		}

		$textcontent = trim((string)$xml);
		if (count($textcontent) > 0)
			$jsnode["_"] = $textcontent;

		foreach ($xml->children() as $childxmlnode)
		{
			$childname = $childxmlnode->getName();
			if (!array_key_exists($childname, $jsnode))
				$jsnode[$childname] = array();
			array_push($jsnode[$childname], xml_to_json($childxmlnode, true));
		}
		return $jsnode;
	}

	else
	{
		$nodename = $xml->getName();
		$jsnode[$nodename] = array();
		array_push($jsnode[$nodename], xml_to_json($xml, true));
		return json_encode($jsnode);
	}
}

/**
 * Retrieve global from the database.
 *
 * @param $name
 * @return mixed|null|static
 */
function get_global($name)
{
	// Retrieve master credentials
	if (env('APP_ENV') == 'staging')
	{
		$db_host = $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];
		$db_database = $_SERVER['RDS_DB_NAME'];
		$db_username = $_SERVER['RDS_USERNAME'];
		$db_password = $_SERVER['RDS_PASSWORD'];
	}
	else
	{
		$db_host = env('DB_HOST', 'localhost');
		$db_database = env('DB_DATABASE', 'forge');
		$db_username = env('DB_USERNAME', 'forge');
		$db_password = env('DB_PASSWORD', '');
	}

	// Connect to the master database
	$db = new DBConnection([
		'host' => $db_host,
		'database' => $db_database,
		'username' => $db_username,
		'password' => $db_password,
	]);

	$option = $db->getConnection()->table('globals')->where('name', $name)->first();

	if (! $option)
		return null;

	return $option->value;
}

/**
 * Get an array of a specific property from a specific collection of objects.
 *
 * @param $resource
 * @param $property
 * @return array
 */
function get_property_list($resource, $property)
{
	$list = [];
	foreach ($resource as $item)
		$list[] = $item->{$property};

	return $list;
}

function object_to_array($object)
{
	if (! is_object($object) && ! is_array($object))
		return $object;

	return array_map('object_to_array', (array) $object);
}

/**
 * Clean all non-ascii characters from a string
 *
 * @param $string
 * @return string
 */
function clean_non_ascii_characters($string)
{
	$text = $string;

	// Single letters
	$text = preg_replace("/[∂άαáàâãªä]/u", "", $text);
	$text = preg_replace("/[∆лДΛдАÁÀÂÃÄ]/u", "", $text);
	$text = preg_replace("/[ЂЪЬБъь]/u", "", $text);
	$text = preg_replace("/[βвВ]/u", "", $text);
	$text = preg_replace("/[çς©с]/u", "", $text);
	$text = preg_replace("/[ÇС]/u", "", $text);
	$text = preg_replace("/[δ]/u", "", $text);
	$text = preg_replace("/[éèêëέëèεе℮ёєэЭ]/u", "", $text);
	$text = preg_replace("/[ÉÈÊË€ξЄ€Е∑]/u", "", $text);
	$text = preg_replace("/[₣]/u", "", $text);
	$text = preg_replace("/[НнЊњ]/u", "", $text);
	$text = preg_replace("/[ђћЋ]/u", "", $text);
	$text = preg_replace("/[ÍÌÎÏ]/u", "", $text);
	$text = preg_replace("/[íìîïιίϊі]/u", "", $text);
	$text = preg_replace("/[Јј]/u", "", $text);
	$text = preg_replace("/[ΚЌК]/u", "", $text);
	$text = preg_replace("/[ќк]/u", "", $text);
	$text = preg_replace("/[ℓ∟]/u", "", $text);
	$text = preg_replace("/[Мм]/u", "", $text);
	$text = preg_replace("/[ñηήηπⁿ]/u", "", $text);
	$text = preg_replace("/[Ñ∏пПИЙийΝЛ]/u", "", $text);
	$text = preg_replace("/[óòôõºöοФσόо]/u", "", $text);
	$text = preg_replace("/[ÓÒÔÕÖθΩθОΩ]/u", "", $text);
	$text = preg_replace("/[ρφрРф]/u", "", $text);
	$text = preg_replace("/[®яЯ]/u", "", $text);
	$text = preg_replace("/[ГЃгѓ]/u", "", $text);
	$text = preg_replace("/[Ѕ]/u", "", $text);
	$text = preg_replace("/[ѕ]/u", "", $text);
	$text = preg_replace("/[Тт]/u", "", $text);
	$text = preg_replace("/[τ†‡]/u", "", $text);
	$text = preg_replace("/[úùûüџμΰµυϋύ]/u", "", $text);
	$text = preg_replace("/[√]/u", "", $text);
	$text = preg_replace("/[ÚÙÛÜЏЦц]/u", "", $text);
	$text = preg_replace("/[Ψψωώẅẃẁщш]/u", "", $text);
	$text = preg_replace("/[ẀẄẂШЩ]/u", "", $text);
	$text = preg_replace("/[ΧχЖХж]/u", "", $text);
	$text = preg_replace("/[ỲΫ¥]/u", "", $text);
	$text = preg_replace("/[ỳγўЎУуч]/u", "", $text);
	$text = preg_replace("/[ζ]/u", "", $text);

	// Punctuation
	$text = preg_replace("/[‚‚]/u", ",", $text);
	$text = preg_replace("/[`‛′’‘]/u", "'", $text);
	$text = preg_replace("/[″“”«»„]/u", '"', $text);
	$text = preg_replace("/[—–―−–‾⌐─↔→←]/u", '-', $text);
	$text = preg_replace("/[  ]/u", ' ', $text);
	$text = str_replace("…", "...", $text);
	$text = str_replace("≠", "!=", $text);
	$text = str_replace("≤", "<=", $text);
	$text = str_replace("≥", ">=", $text);
	$text = preg_replace("/[‗≈≡]/u", "=", $text);

	// Exciting combinations
	$text = str_replace("ыЫ", "bl", $text);
	$text = str_replace("℅", "c/o", $text);
	$text = str_replace("₧", "Pts", $text);
	$text = str_replace("™", "’", $text);
	$text = str_replace("№", "No", $text);
	$text = str_replace("Ч", "4", $text);
	$text = str_replace("‰", "%", $text);
	$text = preg_replace("/[∙•]/u", "*", $text);
	$text = str_replace("‹", "<", $text);
	$text = str_replace("›", ">", $text);
	$text = str_replace("‼", "!!", $text);
	$text = str_replace("⁄", "/", $text);
	$text = str_replace("∕", "/", $text);
	$text = str_replace("⅞", "7/8", $text);
	$text = str_replace("⅝", "5/8", $text);
	$text = str_replace("⅜", "3/8", $text);
	$text = str_replace("⅛", "1/8", $text);
	$text = preg_replace("/[‰]/u", "%", $text);
	$text = preg_replace("/[Љљ]/u", "Ab", $text);
	$text = preg_replace("/[Юю]/u", "IO", $text);
	$text = preg_replace("/[ﬁﬂ]/u", "fi", $text);
	$text = preg_replace("/[зЗ]/u", "3", $text);
	$text = str_replace("£", "(pounds)", $text);
	$text = str_replace("₤", "(lira)", $text);
	$text = preg_replace("/[‰]/u", "%", $text);
	$text = preg_replace("/[↨↕↓↑│]/u", "|", $text);
	$text = preg_replace("/[∞∩∫⌂⌠⌡]/u", "", $text);

	return $text;
}

function displayElapsedTime($date)
{
//	return sprintf('%s %s %s %s',
//				   formatDateInterval('%d', 'day', $date),
//				   formatDateInterval('%h', 'hour', $date),
//				   formatDateInterval('%i', 'minute', $date),
//				   formatDateInterval('%s', 'second', $date)
//	);

	return sprintf('%s %s',
				   formatDateInterval('%d', 'day', $date),
				   formatDateInterval('%h', 'hour', $date)
	);
}

function formatDateInterval($format, $interval, $date)
{
	$count = $date->diff(new DateTime)->format($format);

	return sprintf('%s %s', $count, str_plural($interval, $count));
}